<template>
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Content E-Detailings</h3>
					<div class="card-tools">
						<button
							data-toggle="modal"
							data-target="#modal-content-edetailing"
							class="btn btn-block btn-outline-primary"
						>
							<span>Add</span>
						</button>
					</div>
				</div>
				<div class="card-body">
                    <!-- <vue-element-loading :active="isLoading" :is-full-screen="false"/> -->
                    <ve-table :columns="columns" :table-data="tableData" id="table-container" :sort-option="sortOption" />
                    <div class="table-pagination">
                        <!-- 'sizer', 'jumper' -->
                        <ve-pagination
                            :total="totalCount"
                            :page-index="pageIndex"
                            :page-size="pageSize"
                            @on-page-number-change="pageNumberChange"
                            @on-page-size-change="pageSizeChange"
                            :layout="['total', 'prev', 'pager', 'next']"
                        />
                    </div>
                </div>
			</div>
		</div>

        <!-- Modal Content E-Detailing -->
		<modal
			:id="`modal-content-edetailing`"
			:title="`${modal.title} a Content E-Detailing`"
			:submitFn="submit"
			:loading="isLoading"
		>
			<template :slot="'body'">
				<div class="form-group row">
                    <label class="col-sm-3 col-form-label">Caption</label>
                    <div class="col-sm-8">
                        <input
                            v-model="form.caption"
                            type="text"
                            class="form-control"
                            id="caption"
                            placeholder="Insert a caption"
                            :class="{ 'is-invalid': 'caption' in formValidation || false }"
                        />
                        <div
                            v-if="'caption' in formValidation || false"
                            class="help-block col-xs-12 col-sm-reset inline"
                        >{{ formValidation.caption[0] }}</div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">File (PDF)</label>
                    <div class="col-sm-8">
						<div class="input-group">
							<input class="custom-file-input" type="file" name="file_edetail" id="file_edetail" ref="file_edetail" v-on:change="handleFileUpload()" 
								:class="{ 'is-invalid': 'file_edetail' in formValidation || false }"
							/>
							<label for="file_edetail" class="custom-file-label"></label>
						</div>
						
                        <div
                            v-if="'file_edetail' in formValidation || false"
                            class="help-block col-xs-12 col-sm-reset inline"
                        >{{ formValidation.file_edetail[0] }}</div>
                    </div>
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

    props: {
        brandId: {
            type: Number,
            required: true
        }
    },

    computed: {
        token(){
			let bearerToken = this.$auth.strategy.token.get();
			
			if (!bearerToken) {
				return "";
			}

			let bearerTokenArray = bearerToken.split(" ");

            return bearerTokenArray[1];
        }
    },

    data() {
		return {
			isLoading: false,
            loadingInstance: null,
			columns: [
				{
					field: "",
					key: "a",
					title: "No.",
					width: 50,
					align: "center",
					renderBodyCell: ({ row, column, rowIndex }, h) => {
						// return ++rowIndex;
                        return (this.pageIndex - 1) * this.pageSize + 1 + rowIndex;
					}					
				},
				{ 
                    field: "caption", key: "b", title: "Caption", align: "left",
                    renderBodyCell: ({row, column, rowIndex}, h)=> {
                        const caption = (row[column.field] && row[column.field] != "null") ? row[column.field] : '-';
						const createdAt = this.$moment(row['created_at']).format("lll")
						
						return (<div>
                            <div><strong>{ caption }</strong></div>
                            <div class="text-muted">{createdAt}</div>
                        </div>);
					},
					sortBy: ""
                },
				{
					field: "file_id",
					key: "d",
					title: "File",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return (
                            <button type="button" class="btn btn-app" 
								on-click={()=> this.filePreview(row['id']) }
							>
								<i class="fas fa-file"></i>
								<b>PDF</b>
							</button>
                        )
					},
				},
				{
					field: "",
					key: "f",
					title: "Action",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return (
							<button type="button" class="btn btn-block btn-danger btn-sm" on-click={()=> this.deleteRow(row['id'])}> 
                                <i class="fas fa-trash"></i>
                            </button>
						);
					}
				},
			],
			// page index
			pageIndex: 1,
			// page size
			pageSize: 10,
			totalCount: 0,
			tableData: [],
            searchFilter: "",
			sortParams: {},
			sortOption: {
				// sort always
				sortAlways: true,
				sortChange: (params) => {
					// console.log("sortChange::", params);
					if (Object.keys(params).length) {
						this.sortParams = params;
						this.$nextTick(()=>{
							this.getDataFromApi();
						});
					}
				},
			},

            /**
             * On demand
             */
            form: new Form({
                id: null,
				caption: null,
                file_edetail: null
			}),

            modal: {
                mode: "create",
                title: "Add"
            },

            formValidation: {}
		};
	},

    methods: {
        /**
		 * Required methods
		 * Table data
		 */
		// page number change
		pageNumberChange(pageIndex) {
			this.pageIndex = pageIndex;
            this.getDataFromApi();
		},

		// page size change
		pageSizeChange(pageSize) {
			this.pageIndex = 1;
			this.pageSize = pageSize;
            this.getDataFromApi();
		},

		// Get table data from API
		async getDataFromApi() {
            // this.loadingInstance.show();
			this.isLoading = true;
			try {
				const { data } = await this.$axios.get(`brands/${this.brandId}/content-edetailings`, {
					params: {
						page: this.pageIndex,
						per_page: this.pageSize,
						search: this.searchFilter,
						sort: this.sortParams
					},
				});

				this.totalCount = data.total;
				this.tableData = data.data;
				this.isLoading = false;
				
			} catch (error) {
				this.$toast.global.server_error();
				// console.log("ERROR!!!!!!!!!!!", error);
			}
		},

		deleteRow(id){

			this.$swal.fire({
				title: 'Sure to delete Content E-Detailing?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`brands/${this.brandId}/content-edetailings/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('E-Detailing is not deleted', '', 'info');
				}
			})

			
		},


        /**
         * On demand
         * 
         */
        async submit(){
			this.$nuxt.$loading.start();
            this.isLoading = true;
            try {

    			let formData = new FormData();
    			formData.append("caption", this.form.caption);
    			formData.append("file_edetail", this.form.file_edetail);


                if (this.modal.mode === 'create') {
                    const {data} = await this.$axios.post(
                        `brands/${this.brandId}/content-edetailings`,
                        formData,
                        {
                            headers: { "Content-Type": "multipart/form-data" },
                        }
                    );
                }

                // if (this.modal.mode === 'edit') {
                //     await this.$axios.put(`brands/${this.brandId}/content-edetailings/${this.form.id}`, this.form);
                // }

				this.$nuxt.$loading.finish();
                this.isLoading = false;

                // Close modal
				$('#modal-content-edetailing .close').click();

                // Reset form
                this.resetForm();

                // Reload table
                this.getDataFromApi();

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

        onShowCreateModal(){
            this.modal.mode = "create";
            this.modal.title = "Add";

            this.resetForm();
        },

        resetForm(){
            this.form = new Form({
                id: null,
                caption: null,
                file_edetail: null
            })
            $('.custom-file-label').html('');
			this.formValidation = {};
        },

        async formBinding(id){
            this.$nuxt.$loading.start();
            this.isLoading = true;
            try {
                const {data} = await this.$axios.get(`brands/${this.brandId}/content-edetailings/${id}`);

                if (data.success) {
                    this.form = new Form({
                        id: data.data.id,
                        caption: data.data.caption,
                        file_edetail: data.data.file_id
                    })
                }

				this.$nuxt.$loading.finish();
                this.isLoading = false;


            } catch (error) {
				console.log('ERROR!!', error);
                
                if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.form.errors.set(error.response.data.errors);
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

        handleFileUpload() {
			this.form.file_edetail = this.$refs.file_edetail.files[0];
		},

        async filePreview(id) {
			try {
				this.$nuxt.$loading.start();
                this.isLoading = true;

				this.$axios.get(
					`brands/${this.brandId}/content-edetailings/${id}/file-preview`, {
						responseType: 'blob'
				}).then((response) => {
					window.open(URL.createObjectURL(response.data));
				})

				// var previewUrl = process.env.API_URL + `/brands/${this.brandId}/content-edetailings/${id}/file-preview?token=${this.token}`;
				// window.open(previewUrl);

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
        this.$nextTick(()=> {
            this.getDataFromApi();
        })

		$('#file_edetail').on('change',function(){
			//get the file name
			var fileName = $(this).val();
			//replace the "Choose a file" label
			$(this).next('.custom-file-label').html('..'+fileName.slice(-20));
		})
    }
};
</script>

<style>
</style>