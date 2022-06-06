<template>
  <div class="container-btn-import">

    <button
        data-toggle="modal"
        data-target="#modal-import-employee"
        class="btn btn-outline-primary"
    >
        <span><i class="fas fa-upload"></i> Import (Excel)</span>
    </button>

    <!-- Modal Content E-Detailing -->
    <modal
        :id="`modal-import-employee`"
        :title="`Import Employees`"
        :submitFn="submit"
        :onCloseFn="resetForm"
        :loading="isLoading"
    >
        <template :slot="'body'">
            <div class="callout callout-warning mb-5">
                <p class="text-md">
                    Download this import template for employees. &nbsp;
                    <a href="#" class="blue" @click.prevent="downloadTemplate">
                        <span><i class="fas fa-download text-link"></i> Template</span>
                    </a>
                </p>
            </div>

            <div class="form-group row modal-form-container text-md">
                <label class="col-sm-3 col-form-label">Company Register Code</label>
                <div class="col-sm-9">
                    <input
                        v-model="form.company_register_code"
                        type="text"
                        class="form-control"
                        id="company_register_code"
                        placeholder="Register Code"
                        :class="{ 'is-invalid': 'company_register_code' in formValidation || false }"
                    />
                    <div
                        v-if="'company_register_code' in formValidation || false"
                        class="help-block col-xs-12 col-sm-reset inline text-sm text-danger mt-2"
                    >{{ formValidation.company_register_code[0] }}</div>
                </div>
            </div>
            <div class="form-group row modal-form-container text-md">
                <label class="col-sm-3 col-form-label">File (Excel)</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input class="custom-file-input" type="file" name="file_excel" id="file_excel" ref="file_excel" v-on:change="handleFileUpload()" 
                            :class="{ 'is-invalid': 'file_excel' in formValidation || false }"
                        />
                        <label for="file_excel" class="custom-file-label"></label>
                    </div>
                    
                    <div
                        v-if="'file_excel' in formValidation || false"
                        class="help-block col-xs-12 col-sm-reset inline text-sm text-danger mt-2"
                    >{{ formValidation.file_excel[0] }}</div>
                    <div
                        v-if="'extension' in formValidation || false"
                        class="help-block col-xs-12 col-sm-reset inline text-sm text-danger mt-2"
                    >{{ formValidation.extension[0] }}</div>
                </div>
            </div>

            <div class="callout callout-danger mt-3" v-if="formValidation.length">
                <p class="text-md">
                    <strong>There is validation info. Fix and re-upload!</strong>
                    <ol>
                        <li class="text-danger" v-for="(item, index) in formValidation" :key="index">
                            {{ item }}
                        </li>
                    </ol>
                </p>
            </div>

        </template>
    </modal>

  </div>
</template>

<script>
import Form from "vform";
import Modal from "~/components/Modal";

export default {
    components: {
        Modal
    },

    data(){
        return {
			isLoading: false,
            form: new Form({
                company_register_code: "",
                file_excel: null
			}),
            formValidation: {}

        }
    },

    methods: {
        handleFileUpload() {
			this.form.file_excel = this.$refs.file_excel.files[0];
		},

        async submit(){
			this.$nuxt.$loading.start();
            this.isLoading = true;
            try {

    			let formData = new FormData();
    			formData.append("file_excel", this.form.file_excel);
    			formData.append("company_register_code", this.form.company_register_code);

                const {data} = await this.$axios.post(
                    `imports/excel/employees`,
                    formData,
                    {
                        headers: { "Content-Type": "multipart/form-data" },
                    }
                );

                if (data.success) {
                    this.$emit('importSuccess', data);
                    this.$swal.fire(`${data.message}`, 'Please reload this page if no changes.', 'success');
                }

				this.$nuxt.$loading.finish();
                this.isLoading = false;

                // Close modal
				$('#modal-import-employee .close').click();

                // Reset form
                this.resetForm();

            } catch (error) {
				console.log('ERROR!!', error);
                
                if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.formValidation = error.response.data.errors;
					});

					this.$toast.error(`${error.response.data.message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});	

				} else if(error.response && error.response.status == 403) {
					let message = "Action failed.";

					if (error.response.data) {
						message = error.response.data.message;
					}

					this.$toast.error(`${message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 4000,
					});	
				} else {
					this.$toast.error(`Error on submitting form!.`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});	
				}
			
				this.$nuxt.$loading.finish();
                this.isLoading = false;

            }
        },

        resetForm(){
            this.form = new Form({
                file_excel: null
            })
            $('.custom-file-label').html('');
			this.formValidation = {};
        },

        async downloadTemplate() {
			try {
				this.$nuxt.$loading.start();
                this.isLoading = true;

				var previewUrl = process.env.API_URL + `/import-templates/employee`;
				window.open(previewUrl);

				this.isLoading = true;
				this.$nuxt.$loading.finish();
				
			} catch (err) {
				console.log('ERROOORRR!!', err);

				this.isLoading = false;
				this.$nuxt.$loading.finish();
			}
		}
    },

    mounted(){
		$('#file_excel').on('change',function(){
			//get the file name
			var fileName = $(this).val();
			//replace the "Choose a file" label
			$(this).next('.custom-file-label').html('..'+fileName.slice(-20));
		})
    }
}
</script>

<style scoped>
.container-btn-import{
    display: initial;
}
</style>