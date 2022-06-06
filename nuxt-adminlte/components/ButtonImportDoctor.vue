<template>
  <div class="container-btn-import">

    <button
        data-toggle="modal"
        data-target="#modal-import-doctor"
        class="btn btn-outline-primary"
        title="Import Excel file to Add Doctors using our template."
    >
        <span><i class="fas fa-upload"></i> Import (Excel)</span>
    </button>

    <!-- Modal Content E-Detailing -->
    <modal
        :id="`modal-import-doctor`"
        :title="`Import Doctors`"
        :submitFn="submit"
        :onCloseFn="resetForm"
        :loading="isLoading"
    >
        <template :slot="'body'">
            <div v-if="form.mode == 'create'" class="callout callout-warning mb-5">
                <p class="text-md">
                    Download this import template to add doctors.
                </p>
                <p class="text-md">
                    <a href="#" class="blue" @click.prevent="downloadTemplate">
                        <span><i class="fas fa-download text-link"></i> Template</span>
                    </a>
                </p>
            </div>
            <div v-if="form.mode == 'update'" class="callout callout-danger mb-5">
                <p class="text-md">
                    Excel file based on Export format to edit doctors.
                </p>
            </div>
            <div class="form-group row modal-form-container text-md">
                <label class="col-sm-3 col-form-label">File (Excel)</label>
                <div class="col-sm-8">
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

            <div class="form-group row modal-form-container text-md">
                <label class="col-sm-3 col-form-label">Input Mode</label>
                <div class="col-sm-8 form-group">
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" id="mode-create" type="radio" v-model="form.mode" value="create">
                        <label class="custom-control-label" for="mode-create"><b>Create</b> <span class="text-muted">(Add new doctors)</span></label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" id="mode-update" type="radio" v-model="form.mode" value="update">
                        <label class="custom-control-label" for="mode-update"><b>Update</b> <span class="text-muted">(Edit existing doctors)</span></label>
                    </div>
                    <div
                        v-if="'mode' in formValidation || false"
                        class="help-block col-xs-12 col-sm-reset inline text-sm text-danger mt-2"
                    >{{ formValidation.mode[0] }}</div>
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
                file_excel: null,
                mode: 'create'
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
    			formData.append("mode", this.form.mode);

                const {data} = await this.$axios.post(
                    `imports/excel/doctors`,
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
				$('#modal-import-doctor .close').click();

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

        downloadTemplate(){
			this.loading = true;
			this.$axios.get(`exports/excel/template-export-doctors`, {
				responseType: 'blob'
			}).then((response) => {
				window.open(URL.createObjectURL(response.data));
				this.loading = false;
			}).catch(error=> {
				this.loading = false;
				console.log('Errorrr!!', error);
			})

			
			// var filePath = process.env.apiUrl + `/distribusi-pegawai/export-nomor-surat?token=${this.token}`;
			// window.open(filePath);
		}

        // async downloadTemplate() {
		// 	try {
		// 		this.$nuxt.$loading.start();
        //         this.isLoading = true;

		// 		var previewUrl = process.env.API_URL + `/import-templates/doctor`;
		// 		window.open(previewUrl);

		// 		this.isLoading = true;
		// 		this.$nuxt.$loading.finish();
				
		// 	} catch (err) {
		// 		console.log('ERROOORRR!!', err);

		// 		this.isLoading = false;
		// 		this.$nuxt.$loading.finish();
		// 	}
		// }
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