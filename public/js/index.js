
    const loginPath = document.body.dataset.loginUrl;
    const registerPath = document.body.dataset.registerUrl;
    const registerDraftKey = 'skedyul.register.draft.v1';
    let registerPhonePicker = null;

    function saveRegisterDraft(form) {
      if (!form) return;
      const draft = {};
      [...form.elements].forEach((field) => {
        if (!field.name || field.name === '_token' || field.type === 'password' || field.type === 'file' || field.disabled) return;
        if (field.type === 'checkbox' || field.type === 'radio') {
          if (field.checked) draft[field.name] = field.value;
          return;
        }
        draft[field.name] = field.value;
      });
      try {
        sessionStorage.setItem(registerDraftKey, JSON.stringify(draft));
      } catch (_) {
        // Storage may be unavailable in a private browsing session.
      }
    }

    function restoreRegisterDraft(form) {
      if (!form) return;
      try {
        const draft = JSON.parse(sessionStorage.getItem(registerDraftKey) || '{}');
        Object.entries(draft).forEach(([name, value]) => {
          const field = form.elements.namedItem(name);
          if (!field || field.type === 'password' || field.type === 'file') return;
          if (field instanceof RadioNodeList) {
            [...field].forEach((option) => { option.checked = option.value === value; });
          } else if (field.type === 'checkbox') {
            field.checked = Boolean(value);
          } else {
            field.value = value;
          }
        });
      } catch (_) {
        // Ignore malformed or unavailable saved drafts.
      }
    }

    function initRegisterPhonePicker() {
      const input = document.getElementById('register-phone');
      const fullNumber = document.getElementById('register-phone-full');
      const form = document.getElementById('register-form');
      if (!input || !fullNumber || input.dataset.phoneReady) return;
      input.dataset.phoneReady = 'true';

      if (!window.intlTelInput) {
        // Keep a usable country picker if both hosted library CDNs are offline.
        const countries = [
          ['🇵🇭', 'Philippines', '63'],
          ['🇺🇸', 'United States', '1'],
          ['🇬🇧', 'United Kingdom', '44'],
          ['🇨🇦', 'Canada', '1'],
          ['🇦🇺', 'Australia', '61'],
          ['🇳🇿', 'New Zealand', '64'],
          ['🇸🇬', 'Singapore', '65'],
          ['🇯🇵', 'Japan', '81'],
          ['🇰🇷', 'South Korea', '82'],
          ['🇮🇳', 'India', '91'],
          ['🇦🇪', 'United Arab Emirates', '971'],
          ['🇸🇦', 'Saudi Arabia', '966'],
          ['🇲🇾', 'Malaysia', '60'],
          ['🇮🇩', 'Indonesia', '62'],
          ['🇹🇭', 'Thailand', '66'],
          ['🇻🇳', 'Vietnam', '84'],
          ['🇩🇪', 'Germany', '49'],
          ['🇫🇷', 'France', '33'],
          ['🇮🇹', 'Italy', '39'],
          ['🇪🇸', 'Spain', '34'],
        ];
        const wrapper = document.createElement('div');
        const countryPicker = document.createElement('select');
        wrapper.className = 'register-phone-fallback';
        countryPicker.setAttribute('aria-label', 'Country calling code');
        countries.forEach(([flag, name, dialCode]) => {
          const option = document.createElement('option');
          option.value = dialCode;
          option.textContent = `${flag} ${name} +${dialCode}`;
          option.selected = dialCode === '63' && name === 'Philippines';
          countryPicker.appendChild(option);
        });
        input.parentNode.insertBefore(wrapper, input);
        wrapper.append(countryPicker, input);
        input.placeholder = 'Phone Number';

        const fallbackNumber = () => {
          const digits = input.value.replace(/\D/g, '').slice(0, 10);
          input.value = digits;
          fullNumber.value = digits ? `+${countryPicker.value}${digits.replace(/^0+/, '')}` : '';
        };
        input.addEventListener('input', fallbackNumber);
        countryPicker.addEventListener('change', fallbackNumber);
        form?.addEventListener('submit', fallbackNumber);
        if (fullNumber.value) {
          const matchingCountry = countries.find(([, , dialCode]) => fullNumber.value.startsWith(`+${dialCode}`));
          if (matchingCountry) {
            countryPicker.value = matchingCountry[2];
            input.value = fullNumber.value.slice(matchingCountry[2].length + 1);
          }
        }
        return;
      }

      registerPhonePicker = window.intlTelInput(input, {
        initialCountry: 'ph',
        countryOrder: ['ph'],
        countrySelectorMode: 'DROPDOWN',
        countrySearch: true,
        separateDialCode: true,
        strictMode: true,
        loadUtils: () => import('https://cdn.jsdelivr.net/npm/intl-tel-input@25.14.1/build/js/utils.js'),
      });
      if (fullNumber.value) registerPhonePicker.setNumber(fullNumber.value);

      // getNumber() relies on the optional utils module. Keep a dial-code
      // fallback active until that module has finished loading.
      let phoneUtilsReady = false;
      registerPhonePicker.promise?.then(() => {
        phoneUtilsReady = true;
        syncPhoneNumber();
      }).catch(() => {});

      const syncPhoneNumber = () => {
        const digits = input.value.replace(/\D/g, '');
        if (digits.length > 10) {
          input.value = digits.slice(0, 10);
        }
        if (!input.value.trim()) {
          fullNumber.value = '';
          return;
        }
        if (phoneUtilsReady) {
          try {
            const formatted = registerPhonePicker.getNumber();
            if (formatted && formatted.startsWith('+')) {
              fullNumber.value = formatted;
              return;
            }
          } catch (_) {
            // Use the selected country's calling code while utils initialize.
          }
        }
        const dialCode = registerPhonePicker.getSelectedCountryData()?.dialCode || '63';
        fullNumber.value = `+${dialCode}${input.value.replace(/\D/g, '').replace(/^0+/, '').slice(0, 10)}`;
      };
      input.addEventListener('input', syncPhoneNumber);
      input.addEventListener('countrychange', syncPhoneNumber);
      form?.addEventListener('submit', syncPhoneNumber);
      syncPhoneNumber();
    }

    function setAuthView(register, updateUrl = true) {
      document.getElementById('login-view').hidden = register;
      document.getElementById('register-view').hidden = !register;
      const panel = document.getElementById('auth-panel');
      panel.classList.toggle('w-[600px]', !register);
      panel.classList.toggle('w-[760px]', register);
      document.body.classList.toggle('h-screen', !register);
      document.body.classList.toggle('overflow-hidden', !register);
      document.body.classList.toggle('min-h-screen', register);
      document.body.classList.toggle('overflow-y-auto', register);
      document.title = register ? 'SKEDYUL — Register' : 'SKEDYUL — Login';
      if (register) requestAnimationFrame(initRegisterPhonePicker);
      if (updateUrl) history.pushState({
        register
      }, '', register ? registerPath : loginPath);
    }

    window.addEventListener('popstate', () => setAuthView(location.pathname === registerPath, false));

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
      button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.target);
        if (!input) return;
        const showPassword = input.type === 'password';
        input.type = showPassword ? 'text' : 'password';
        const label = button.dataset.target === 'confirm-password'
          ? (showPassword ? 'Hide confirm password' : 'Show confirm password')
          : (showPassword ? 'Hide password' : 'Show password');
        button.setAttribute('aria-label', label);
        button.title = label;
        button.querySelector('[data-eye-slash]')?.classList.toggle('hidden', !showPassword);
      });
    });

    document.querySelectorAll('[data-auth-view]').forEach((link) => {
      link.addEventListener('click', (event) => {
        event.preventDefault();
        setAuthView(link.dataset.authView === 'register');
      });
    });

    document.addEventListener('DOMContentLoaded', () => {
      const password = document.getElementById('register-password');
      const confirmation = document.getElementById('confirm-password');
      const feedback = document.getElementById('password-match');
      const form = document.getElementById('register-form');

      function passwordsMatch() {
        if (!password || !confirmation || !feedback) return true;
        if (!confirmation.value) {
          confirmation.setCustomValidity('');
          feedback.textContent = '';
          return true;
        }
        const matches = password.value === confirmation.value;
        confirmation.setCustomValidity(matches ? '' : 'Passwords do not match.');
        feedback.textContent = confirmation.value ? (matches ? 'Passwords match.' : 'Passwords do not match.') : '';
        feedback.className = 'mt-1 min-h-3 text-[10px] ' + (matches ? 'text-emerald-600' : 'text-red-600');
        return matches;
      }

      password?.addEventListener('input', passwordsMatch);
      confirmation?.addEventListener('input', passwordsMatch);
      form?.addEventListener('submit', (event) => {
        if (!passwordsMatch()) {
          event.preventDefault();
          confirmation?.reportValidity();
          return;
        }
        const button = document.getElementById('register-submit');
        button.disabled = true;
        button.textContent = 'Submitting for approval…';
      });

      const registerView = document.getElementById('register-view');
      if (document.getElementById('registration-success')) {
        try { sessionStorage.removeItem(registerDraftKey); } catch (_) {}
      } else {
        restoreRegisterDraft(form);
      }

      if (!registerView.hidden) initRegisterPhonePicker();

      form?.addEventListener('input', () => saveRegisterDraft(form));
      form?.addEventListener('change', () => saveRegisterDraft(form));

      const college = document.getElementById('register-college');
      const department = document.getElementById('register-department');
      if (college && department) {
        const selectedDepartment = department.value;
        const departmentOptions = [...department.options]
          .filter((option) => option.value)
          .map((option) => option.cloneNode(true));

        const updateDepartmentOptions = (preserveSelection = false) => {
          const matchingDepartments = departmentOptions.filter(
            (option) => option.dataset.college === college.value
          );
          const placeholder = document.createElement('option');
          placeholder.value = '';
          placeholder.textContent = !college.value
            ? 'Select a college first'
            : matchingDepartments.length
              ? 'Select program'
              : 'No programs available for this college';
          placeholder.selected = true;

          department.replaceChildren(placeholder, ...matchingDepartments);
          department.disabled = !college.value || matchingDepartments.length === 0;

          if (preserveSelection && selectedDepartment) {
            const previousSelection = matchingDepartments.find(
              (option) => option.value === selectedDepartment
            );

            if (previousSelection) department.value = previousSelection.value;
          }
        };

        college.addEventListener('change', () => updateDepartmentOptions());
        updateDepartmentOptions(true);
      }
    });
  
