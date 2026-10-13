const passwordToggle = document.querySelector('[data-password-toggle]');
const passwordInput = document.querySelector('#password');

passwordToggle?.addEventListener('click', () => {
    if (!(passwordInput instanceof HTMLInputElement)) {
        return;
    }

    const shouldShowPassword = passwordInput.type === 'password';

    passwordInput.type = shouldShowPassword ? 'text' : 'password';
    passwordToggle.setAttribute('aria-pressed', String(shouldShowPassword));
    passwordToggle.setAttribute('aria-label', shouldShowPassword ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
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
