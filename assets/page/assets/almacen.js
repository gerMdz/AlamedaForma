// store.js
import { ref } from 'vue';

export const store = {
    responseData: ref(null),
    termsAccepted: ref(false),
    setResponseData(data) {
        this.responseData.value = data;
    },
    setTermsAccepted(val) {
        this.termsAccepted.value = !!val;
    }
};