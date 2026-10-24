const passkeyElements = {
  status: () => document.querySelectorAll('[data-passkey-status]'),
  result: () => document.querySelectorAll('[data-passkey-result]'),
  buttons: () => document.querySelectorAll('[data-passkey-register]'),
  loginButtons: () => document.querySelectorAll('[data-passkey-login]'),
};

function setPasskeyText(selector, message) {
  passkeyElements[selector]().forEach((element) => {
    element.textContent = message;
  });
}

function showLoginPasskeyError(message) {
  const error = document.querySelector('[data-passkey-login-error]');
  if (!error) return;
  error.textContent = message;
  error.hidden = !message;
}

function setPasskeyButtonsDisabled(disabled) {
  [...passkeyElements.buttons(), ...passkeyElements.loginButtons()].forEach((button) => {
    button.disabled = disabled;
    button.classList.toggle('opacity-50', disabled);
    button.classList.toggle('cursor-not-allowed', disabled);
  });
}

function base64urlToBytes(value) {
  const base64 = value.replace(/-/g, '+').replace(/_/g, '/');
  const padded = base64.padEnd(Math.ceil(base64.length / 4) * 4, '=');
  const binary = atob(padded);
  return Uint8Array.from(binary, (character) => character.charCodeAt(0));
}

function bytesToBase64url(value) {
  const bytes = new Uint8Array(value);
  let binary = '';
  bytes.forEach((byte) => { binary += String.fromCharCode(byte); });
  return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/g, '');
}

function prepareCreationOptions(options) {
  if (typeof PublicKeyCredential.parseCreationOptionsFromJSON === 'function') {
    return PublicKeyCredential.parseCreationOptionsFromJSON(options);
  }

  return {
    ...options,
    challenge: base64urlToBytes(options.challenge),
    user: { ...options.user, id: base64urlToBytes(options.user.id) },
    excludeCredentials: (options.excludeCredentials || []).map((item) => ({
      ...item,
      id: base64urlToBytes(item.id),
    })),
  };
}

function prepareRequestOptions(options) {
  if (typeof PublicKeyCredential.parseRequestOptionsFromJSON === 'function') {
    return PublicKeyCredential.parseRequestOptionsFromJSON(options);
  }

  return {
    ...options,
    challenge: base64urlToBytes(options.challenge),
    allowCredentials: (options.allowCredentials || []).map((item) => ({
      ...item,
      id: base64urlToBytes(item.id),
    })),
  };
}

function serializeCredential(credential) {
  if (typeof credential.toJSON === 'function') return credential.toJSON();

  const response = credential.response;
  const responseJson = {
    clientDataJSON: bytesToBase64url(response.clientDataJSON),
  };
  if ('attestationObject' in response) {
    responseJson.attestationObject = bytesToBase64url(response.attestationObject);
    responseJson.transports = typeof response.getTransports === 'function' ? response.getTransports() : [];
  } else {
    responseJson.authenticatorData = bytesToBase64url(response.authenticatorData);
    responseJson.signature = bytesToBase64url(response.signature);
    responseJson.userHandle = response.userHandle ? bytesToBase64url(response.userHandle) : null;
  }

  return {
    id: credential.id,
    rawId: bytesToBase64url(credential.rawId),
    type: credential.type,
    response: responseJson,
    clientExtensionResults: credential.getClientExtensionResults(),
    authenticatorAttachment: credential.authenticatorAttachment,
  };
}

function ensurePasskeyDialog() {
  let dialog = document.querySelector('[data-passkey-dialog]');
  if (dialog) return dialog;

  dialog = document.createElement('div');
  dialog.dataset.passkeyDialog = '';
  dialog.className = 'fixed inset-0 z-[10000] hidden items-center justify-center bg-black/70 p-4';
  dialog.innerHTML = `
    <section role="dialog" aria-modal="true" aria-labelledby="passkey-dialog-title"
      class="w-full max-w-lg overflow-hidden rounded-xl bg-black text-white shadow-2xl">
      <header class="flex items-center justify-between border-b border-white/10 px-5 py-4">
        <h2 id="passkey-dialog-title" class="text-sm font-medium">Passkey setup</h2>
        <button type="button" data-passkey-close aria-label="Close" class="rounded px-2 text-2xl leading-none text-white/70 hover:text-white">×</button>
      </header>
      <div class="flex min-h-[330px] flex-col items-center justify-center px-7 py-8 text-center">
        <div data-passkey-icon class="mb-7 text-blue-500">
          <svg viewBox="0 0 120 120" class="h-28 w-28" fill="none" aria-hidden="true">
            <path d="M60 11c-24 0-43 19-43 43m86 0c0-24-19-43-43-43M28 28c-9 8-14 19-14 32m92 0c0-13-5-24-14-32M29 92c8 11 18 17 31 18m31-18c-8 11-18 17-31 18M40 25c-10 6-16 17-16 29m72 0c0-12-6-23-16-29M37 83c6 9 13 13 23 14m23-14c-6 9-13 13-23 14M47 39c-6 4-9 10-9 18v10m44-10c0-8-3-14-9-18M47 77c3 5 7 7 13 8m13-8c-3 5-7 7-13 8m-9-35v14c0 8 3 13 9 17 6-4 9-9 9-17V50c0-7-4-11-9-11s-9 4-9 11Z" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <p data-passkey-dialog-message aria-live="polite" class="text-xl font-semibold">Follow the prompt to register your passkey</p>
        <p data-passkey-dialog-detail class="mt-3 max-w-sm text-sm text-white/65">Your device may ask for your fingerprint, face, PIN, or security key.</p>
      </div>
    </section>`;
  document.body.appendChild(dialog);

  dialog.querySelector('[data-passkey-close]').addEventListener('click', () => {
    dialog.classList.add('hidden');
    dialog.classList.remove('flex');
  });
  return dialog;
}

function showPasskeyDialog(message, detail, successful = false) {
  const dialog = ensurePasskeyDialog();
  const icon = dialog.querySelector('[data-passkey-icon]');
  dialog.querySelector('[data-passkey-dialog-message]').textContent = message;
  dialog.querySelector('[data-passkey-dialog-detail]').textContent = detail;
  icon.innerHTML = successful
    ? '<span class="inline-flex h-24 w-24 items-center justify-center rounded-full border-4 border-emerald-400 text-6xl text-emerald-400" aria-hidden="true">☺</span>'
    : `<svg viewBox="0 0 120 120" class="h-28 w-28" fill="none" aria-hidden="true"><path d="M60 11c-24 0-43 19-43 43m86 0c0-24-19-43-43-43M28 28c-9 8-14 19-14 32m92 0c0-13-5-24-14-32M29 92c8 11 18 17 31 18m31-18c-8 11-18 17-31 18M40 25c-10 6-16 17-16 29m72 0c0-12-6-23-16-29M37 83c6 9 13 13 23 14m23-14c-6 9-13 13-23 14M47 39c-6 4-9 10-9 18v10m44-10c0-8-3-14-9-18M47 77c3 5 7 7 13 8m13-8c-3 5-7 7-13 8m-9-35v14c0 8 3 13 9 17 6-4 9-9 9-17V50c0-7-4-11-9-11s-9 4-9 11Z" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
  icon.classList.toggle('text-emerald-400', successful);
  icon.classList.toggle('text-blue-500', !successful);
  dialog.classList.remove('hidden');
  dialog.classList.add('flex');
}

async function postJson(url, body) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const response = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
    },
    body: JSON.stringify(body),
  });
  const result = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(result.message || 'The request could not be completed.');
  return result;
}

function formatPasskeyDate(value) {
  if (!value) return 'Not used yet';
  const date = new Date(value);
  return Number.isNaN(date.getTime()) ? 'Date unavailable' : date.toLocaleString();
}

async function loadRegisteredPasskeys() {
  const container = document.querySelector('[data-passkey-list]');
  if (!container) return;
  container.replaceChildren();

  try {
    const response = await fetch(container.dataset.listUrl, {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' },
    });
    if (!response.ok) throw new Error('Could not load registered passkeys.');
    const credentials = await response.json();

    if (!credentials.length) {
      const empty = document.createElement('p');
      empty.className = 'text-sm text-slate-500';
      empty.textContent = 'No passkeys registered yet.';
      container.appendChild(empty);
      return;
    }

    credentials.forEach((credential) => {
      const row = document.createElement('div');
      row.className = 'flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 px-3 py-3 dark:border-slate-700';

      const info = document.createElement('div');
      info.className = 'min-w-0';
      const name = document.createElement('div');
      name.className = 'truncate text-sm font-semibold text-slate-800 dark:text-slate-100';
      name.textContent = credential.device_name || 'Passkey device';
      const dates = document.createElement('div');
      dates.className = 'mt-1 text-xs text-slate-500 dark:text-slate-400';
      dates.textContent = `Added ${formatPasskeyDate(credential.created_at)} · Last used ${formatPasskeyDate(credential.last_used_at)}`;
      info.append(name, dates);

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.className = 'rounded-md px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 disabled:opacity-50 dark:hover:bg-red-950/30';
      remove.textContent = 'Remove';
      remove.addEventListener('click', async () => {
        if (!window.confirm(`Remove the passkey “${credential.device_name || 'Passkey device'}” from your account?`)) return;
        remove.disabled = true;
        try {
          const url = container.dataset.deleteUrlTemplate.replace('__CREDENTIAL_ID__', encodeURIComponent(credential.id));
          const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
          const deleted = await fetch(url, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json',
              ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
            },
          });
          if (!deleted.ok) {
            const payload = await deleted.json().catch(() => ({}));
            throw new Error(payload.message || 'Could not remove this passkey.');
          }
          await loadRegisteredPasskeys();
        } catch (error) {
          window.alert(error.message || 'Could not remove this passkey.');
          remove.disabled = false;
        }
      });

      row.append(info, remove);
      container.appendChild(row);
    });
  } catch (error) {
    const failure = document.createElement('p');
    failure.className = 'text-sm text-red-600';
    failure.textContent = error.message || 'Could not load registered passkeys.';
    container.appendChild(failure);
  }
}

async function registerPasskey(button) {
  const result = document.querySelector('[data-passkey-result]');
  const originalText = button.textContent;
  button.disabled = true;
  button.textContent = 'Starting…';
  if (result) result.textContent = '';

  try {
    const options = await postJson(button.dataset.optionsUrl, {});
    const credential = await navigator.credentials.create({ publicKey: prepareCreationOptions(options) });
    if (!credential) throw new Error('Passkey setup was cancelled.');

    showPasskeyDialog('Saving your passkey…', 'Checking your device response securely.');
    await postJson(button.dataset.verifyUrl, {
      credential: serializeCredential(credential),
      device_name: 'This device',
    });

    showPasskeyDialog('Passkey registered successfully', 'You can use this device’s fingerprint, face, PIN, or security key when signing in.', true);
    if (result) result.textContent = 'This device is ready for passkey sign-in.';
    await loadRegisteredPasskeys();
    window.setTimeout(() => {
      const dialog = document.querySelector('[data-passkey-dialog]');
      if (dialog) {
        dialog.classList.add('hidden');
        dialog.classList.remove('flex');
      }
    }, 1800);
  } catch (error) {
    const message = error.name === 'NotAllowedError'
      ? 'Passkey setup was cancelled or timed out. You can try again.'
      : error.message || 'Could not register this passkey. Please try again.';
    if (result) result.textContent = message;
    const dialog = document.querySelector('[data-passkey-dialog]');
    if (dialog && !dialog.classList.contains('hidden')) {
      dialog.querySelector('[data-passkey-dialog-message]').textContent = 'Passkey setup did not finish';
      dialog.querySelector('[data-passkey-dialog-detail]').textContent = message;
    }
  } finally {
    button.disabled = false;
    button.textContent = originalText;
  }
}

async function signInWithPasskey(button) {
  const result = document.querySelector('[data-passkey-result]');
  const emailField = document.querySelector('input[name="email"]');
  const email = emailField?.value.trim() || '';
  if (!email) {
    showLoginPasskeyError('Enter your email address first, then try passkey sign-in.');
    emailField?.focus();
    return;
  }

  const originalText = button.textContent.trim();
  button.disabled = true;
  button.textContent = 'Waiting for your device…';
  if (result) result.textContent = '';
  showLoginPasskeyError('');

  try {
    const options = await postJson(button.dataset.optionsUrl, { email });
    const credential = await navigator.credentials.get({ publicKey: prepareRequestOptions(options) });
    if (!credential) throw new Error('Passkey sign-in was cancelled.');

    showPasskeyDialog('Checking your passkey…', 'Verifying your sign-in securely.');
    const response = await postJson(button.dataset.verifyUrl, {
      credential: serializeCredential(credential),
    });

    showPasskeyDialog('You’re signed in!', 'Your passkey was verified. Opening your SKEDYUL account.', true);
    if (result) result.textContent = 'Passkey verified. Opening your account…';
    window.setTimeout(() => { window.location.assign(response.redirect); }, 900);
  } catch (error) {
    const message = error.name === 'NotAllowedError'
      ? 'No passkey is available for that account. Use your password or register a passkey in Settings.'
      : error.message || 'Could not sign in with this passkey. Please use your password or try again.';
    showLoginPasskeyError(message);
    const dialog = document.querySelector('[data-passkey-dialog]');
    if (dialog && !dialog.classList.contains('hidden')) {
      dialog.querySelector('[data-passkey-dialog-message]').textContent = 'Passkey sign-in did not finish';
      dialog.querySelector('[data-passkey-dialog-detail]').textContent = message;
    }
  } finally {
    button.disabled = false;
    button.textContent = originalText;
  }
}

async function checkPasskeySupport() {
  const statusMessages = passkeyElements.status();
  if (!statusMessages.length && !passkeyElements.buttons().length) return;

  if (!window.isSecureContext) {
    setPasskeyText('status', 'Passkeys need HTTPS, or localhost during development.');
    setPasskeyButtonsDisabled(true);
    return;
  }

  if (!window.PublicKeyCredential || !navigator.credentials) {
    setPasskeyText('status', 'This browser does not support passkeys.');
    setPasskeyButtonsDisabled(true);
    return;
  }

  let hasBuiltInAuthenticator = false;
  try {
    if (typeof PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable === 'function') {
      hasBuiltInAuthenticator = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
    }
  } catch {
    // Passkey support can still work through a security key or browser prompt.
  }

  setPasskeyText('status', hasBuiltInAuthenticator
    ? 'Passkeys are supported. Your device may verify with Windows Hello, a PIN, or another screen-lock method.'
    : 'Passkeys are supported. A compatible USB security key or another device authenticator may be used.');
  setPasskeyButtonsDisabled(false);
}

document.addEventListener('DOMContentLoaded', () => {
  checkPasskeySupport();
  passkeyElements.buttons().forEach((button) => {
    button.addEventListener('click', () => registerPasskey(button));
  });
  passkeyElements.loginButtons().forEach((button) => {
    button.addEventListener('click', () => signInWithPasskey(button));
  });
  loadRegisteredPasskeys();
});
