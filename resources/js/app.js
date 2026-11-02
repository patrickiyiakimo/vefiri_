import { initAuthModal } from './modules/auth-modal';
import { initBackToTop } from './modules/back-to-top';
import { initCarousels } from './modules/carousel';
import { initCart } from './modules/cart';
import { initCountdowns } from './modules/countdown';
import { initDrawers } from './modules/drawer';
import { initDropdowns } from './modules/dropdown';
import { initNewsletters } from './modules/newsletter';
import { initSearch } from './modules/search';
import { initTabs } from './modules/tabs';
import { initToasts } from './modules/toast';
import { initWishlist } from './modules/wishlist';

const initStorefront = () => {
    initToasts();
    initDropdowns();
    initDrawers();
    initAuthModal();
    initCarousels();
    initTabs();
    initCart();
    initSearch();
    initWishlist();
    initCountdowns();
    initNewsletters();
    initBackToTop();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStorefront);
} else {
    initStorefront();
}
