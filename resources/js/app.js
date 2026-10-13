document.querySelectorAll('[data-password-toggle]').forEach((passwordToggle) => {
    passwordToggle.addEventListener('click', () => {
        const targetId = passwordToggle.getAttribute('data-password-target') ?? 'password';
        const passwordInput = document.querySelector(`#${targetId}`);

        if (!(passwordInput instanceof HTMLInputElement)) {
            return;
        }

        const shouldShowPassword = passwordInput.type === 'password';

        passwordInput.type = shouldShowPassword ? 'text' : 'password';
        passwordToggle.setAttribute('aria-pressed', String(shouldShowPassword));
        passwordToggle.setAttribute('aria-label', shouldShowPassword ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
    });
});

const loginForm = document.querySelector('[data-login-form]');
const submitButton = document.querySelector('[data-submit-button]');
const submitLabel = document.querySelector('[data-submit-label]');

loginForm?.addEventListener('submit', () => {
    if (submitButton instanceof HTMLButtonElement) {
        submitButton.disabled = true;
    }

    if (submitLabel) {
        submitLabel.textContent = 'در حال ورود...';
    }
});

const sidebar = document.querySelector('[data-sidebar]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');

const closeSidebar = () => {
    sidebar?.classList.add('translate-x-full');
    sidebarOverlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => {
    sidebar?.classList.remove('translate-x-full');
    sidebarOverlay?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
});

document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeSidebar);
sidebarOverlay?.addEventListener('click', closeSidebar);

document.querySelector('[data-alert-close]')?.addEventListener('click', () => {
    document.querySelector('[data-alert]')?.remove();
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const message = form.getAttribute('data-confirm');

        if (message && ! window.confirm(message)) {
            event.preventDefault();
        }
    });
});
