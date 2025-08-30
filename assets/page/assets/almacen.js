// store.js
import { ref } from 'vue';

export const store = {
    responseData: ref(null),
    termsAccepted: ref(false),
    resultsMode: ref(false),
    hydrate() {
        try {
            const ta = localStorage.getItem('termsAccepted');
            if (ta !== null) this.termsAccepted.value = ta === 'true';
            const rd = localStorage.getItem('responseData');
            if (rd) this.responseData.value = JSON.parse(rd);
            const rm = localStorage.getItem('resultsMode');
            if (rm !== null) this.resultsMode.value = rm === 'true';
        } catch(e) { /* noop */ }
    },
    setResponseData(data) {
        this.responseData.value = data || null;
        try {
            if (data) {
                localStorage.setItem('responseData', JSON.stringify(data));
            } else {
                localStorage.removeItem('responseData');
                // if person cleared, also clear results mode and termsAccepted
                this.setResultsMode(false);
                this.setTermsAccepted(false);
            }
        } catch(e) { /* noop */ }
    },
    setTermsAccepted(val) {
        const v = !!val;
        this.termsAccepted.value = v;
        try { localStorage.setItem('termsAccepted', v ? 'true' : 'false'); } catch(e) { /* noop */ }
    },
    setResultsMode(val) {
        const v = !!val;
        this.resultsMode.value = v;
        try { localStorage.setItem('resultsMode', v ? 'true' : 'false'); } catch(e) { /* noop */ }
    }
};