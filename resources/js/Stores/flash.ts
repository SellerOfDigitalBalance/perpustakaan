import { reactive } from 'vue';

export const flashStore = reactive({
    message: '',
    show: false,

    setMessage(msg: string) {
        this.message = msg;
        this.show = true;

        setTimeout(() => {
            this.show = false;
        }, 3000);
    },
});
