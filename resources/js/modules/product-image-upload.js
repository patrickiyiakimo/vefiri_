const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
const maxFileSize = 4 * 1024 * 1024;

const csrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
};

const setStatus = (element, message, isError = false) => {
    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.remove('hidden', 'text-brand-700', 'text-choice-dark');
    element.classList.add(isError ? 'text-brand-700' : 'text-choice-dark');
};

const clearUploadFields = (form) => {
    form.querySelectorAll('[data-cloudinary-token], [data-cloudinary-version], [data-cloudinary-signature]').forEach((input) => {
        input.value = '';
    });
};

const requestSignature = async (url) => {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to prepare the image upload. Please try again.');
    }

    return payload;
};

const uploadImage = async (signature, file) => {
    const body = new FormData();

    body.append('file', file);
    body.append('api_key', signature.apiKey);
    body.append('timestamp', String(signature.timestamp));
    body.append('public_id', signature.publicId);
    body.append('signature', signature.signature);

    const response = await fetch(signature.url, {
        method: 'POST',
        body,
    });
    const result = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(result.error?.message ?? 'The image could not be uploaded. Please try again.');
    }

    return result;
};

export function initProductImageUpload() {
    document.querySelectorAll('[data-cloudinary-product-form]').forEach((form) => {
        const fileInput = form.querySelector('[data-cloudinary-file]');
        const tokenInput = form.querySelector('[data-cloudinary-token]');
        const versionInput = form.querySelector('[data-cloudinary-version]');
        const signatureInput = form.querySelector('[data-cloudinary-signature]');
        const status = form.querySelector('[data-cloudinary-status]');
        const preview = form.querySelector('[data-cloudinary-preview]');
        const submitButton = form.querySelector('button[type="submit"]');

        if (!fileInput || !tokenInput || !versionInput || !signatureInput || !form.dataset.cloudinarySignatureUrl) {
            return;
        }

        const originalPreviewSrc = preview?.getAttribute('src') ?? '';
        const originalButtonLabel = submitButton?.textContent.trim() ?? '';
        let readyToSubmit = false;

        fileInput.addEventListener('change', () => {
            clearUploadFields(form);
            setStatus(status, '');

            if (preview && !fileInput.files?.length && originalPreviewSrc) {
                preview.src = originalPreviewSrc;
            }
        });

        form.addEventListener('submit', async (event) => {
            if (readyToSubmit) {
                readyToSubmit = false;

                return;
            }

            const file = fileInput.files?.[0];

            if (!file) {
                return;
            }

            event.preventDefault();

            if (!allowedTypes.includes(file.type)) {
                setStatus(status, 'Choose a JPG, PNG or WebP image.', true);

                return;
            }

            if (file.size > maxFileSize) {
                setStatus(status, 'The image must be 4MB or smaller.', true);

                return;
            }

            clearUploadFields(form);

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Uploading image...';
            }

            try {
                setStatus(status, 'Uploading image...');

                const signature = await requestSignature(form.dataset.cloudinarySignatureUrl);
                const result = await uploadImage(signature, file);

                if (result.public_id !== signature.publicId) {
                    throw new Error('Cloudinary returned an unexpected image. Please try again.');
                }

                tokenInput.value = signature.token;
                versionInput.value = String(result.version);
                signatureInput.value = result.signature;

                if (preview && result.secure_url) {
                    preview.src = result.secure_url;
                    preview.classList.remove('hidden');
                }

                setStatus(status, 'Image uploaded. Saving product...');
                readyToSubmit = true;
                form.requestSubmit();
            } catch (error) {
                clearUploadFields(form);
                setStatus(status, error instanceof Error ? error.message : 'The image could not be uploaded. Please try again.', true);
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonLabel;
                }
            }
        });
    });
}
