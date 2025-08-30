// store.js
import { ref } from 'vue';

export const store = {
    responseData: ref(null),
    termsAccepted: ref(false),
    hydrate() {
        try {
            const ta = localStorage.getItem('termsAccepted');
            if (ta !== null) this.termsAccepted.value = ta === 'true';
            const rd = localStorage.getItem('responseData');
            if (rd) this.responseData.value = JSON.parse(rd);
        } catch(e) { /* noop */ }
    },
    setResponseData(data) {
        this.responseData.value = data || null;
        try {
            if (data) {
                localStorage.setItem('responseData', JSON.stringify(data));
            } else {
                localStorage.removeItem('responseData');
            }
        } catch(e) { /* noop */ }
    },
    setTermsAccepted(val) {
        const v = !!val;
        this.termsAccepted.value = v;
        try { localStorage.setItem('termsAccepted', v ? 'true' : 'false'); } catch(e) { /* noop */ }
    }
};