export const errorsMixin = {
    methods: {
        errorClass(item) {
          return { 'form-control': true, 'is-invalid': this.validationErrors ? this.validationErrors.hasOwnProperty(item) : false }
        },
        buttonClass() {
            return ['btn',  'btn-primary', this.saving ? 'disabled' : '']
        },
        handleError(error) {
            this.saving = false
            if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
            } else {
                alert(error.response.data.message)
            }
        },

    },
    computed: {
        hasErrors() {
            return _.isEmpty(this.validationErrors)
        }
    },
}
