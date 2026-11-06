import { initAuthModal } from './modules/auth-modal';
import { initBackToTop } from './modules/back-to-top';
import { initCarousels } from './modules/carousel';
import { initCart } from './modules/cart';
import { initCheckout } from './modules/checkout';
import { initCountdowns } from './modules/countdown';
import { initDrawers } from './modules/drawer';
import { initDropdowns } from './modules/dropdown';
import { initLimitModal } from './modules/limit-modal';
import { initLoadMore } from './modules/load-more';
import { initNewsletters } from './modules/newsletter';
import { initPasswordToggles } from './modules/password-toggle';
import { initSearch } from './modules/search';
import { initTabs } from './modules/tabs';
import { initToasts } from './modules/toast';
import { initWishlist } from './modules/wishlist';

const initStorefront = () => {
    initToasts();
    initDropdowns();
    initDrawers();
    initAuthModal();
    initLimitModal();
    initPasswordToggles();
    initCarousels();
    initTabs();
    initLoadMore();
    initCart();
    initCheckout();
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
