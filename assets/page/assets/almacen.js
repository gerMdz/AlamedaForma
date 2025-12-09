// store.js
import { ref } from 'vue';

export const store = {
    responseData: ref(null),
    termsAccepted: ref(false),
    resultsMode: ref(false),
    editPersonalMode: ref(false),
    // Pestaña activa global (F, O, R, M) — única fuente de verdad
    activeTab: ref('F'),
    // Flags de habilitación por identificador
    hasT: ref(false),
    hasF: ref(false),
    hasO: ref(false),
    hasR: ref(false),
    hasM: ref(false),
    hydrate() {
        try {
            const ta = localStorage.getItem('termsAccepted');
            if (ta !== null) this.termsAccepted.value = ta === 'true';
            const rd = localStorage.getItem('responseData');
            if (rd) this.responseData.value = JSON.parse(rd);
            const rm = localStorage.getItem('resultsMode');
            if (rm !== null) this.resultsMode.value = rm === 'true';
            const at = localStorage.getItem('activeTab');
            if (at && ['F','O','R','M'].includes(at)) this.activeTab.value = at;
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
    },
    setActiveTab(val) {
        const t = String(val || '').toUpperCase();
        if (['F','O','R','M'].includes(t)) {
            this.activeTab.value = t;
            try { localStorage.setItem('activeTab', t); } catch(e) { /* noop */ }
        }
    },
    setHabilitacionFlags(flags) {
        const f = flags || {};
        this.hasT.value = !!(f.T || f.t);
        this.hasF.value = !!(f.F || f.f);
        this.hasO.value = !!(f.O || f.o);
        this.hasR.value = !!(f.R || f.r);
        this.hasM.value = !!(f.M || f.m);
    },
    clearAll() {
        try {
            localStorage.removeItem('responseData');
            localStorage.removeItem('termsAccepted');
            localStorage.removeItem('resultsMode');
        } catch(e) { /* noop */ }
        this.responseData.value = null;
        this.setTermsAccepted(false);
        this.setResultsMode(false);
        try { if (this.editPersonalMode) this.editPersonalMode.value = false; } catch(e) { /* noop */ }
    }
};