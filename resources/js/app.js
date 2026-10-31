import './bootstrap';
import './webauthn';

if (document.cookie.split('; ').some(cookie => cookie === 'skedyul_login=1')) {
    document.cookie = 'skedyul_login=; Max-Age=0; path=/';
    history.replaceState(null, '', location.href);
    history.pushState(null, '', location.href);
}