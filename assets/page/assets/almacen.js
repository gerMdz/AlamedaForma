// store.js
import { ref } from 'vue';

export const store = {
    responseData: ref(null),
    setResponseData(data) {
        this.responseData.value = data;
    }
};