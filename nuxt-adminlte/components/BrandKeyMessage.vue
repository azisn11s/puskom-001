<template>
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Key Messages</h3>
                    <div class="card-tools">
						<button
							data-toggle="modal"
							data-target="#modal-key-message"
							class="btn btn-block btn-outline-primary" 
                            @click="onShowCreateModal"
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

        <!-- Modal Key Message -->
		<modal
			:id="`modal-key-message`"
			:title="`${modal.title} a Key Message`"
			:submitFn="submit"
			:loading="isLoading"
		>
			<template :slot="'body'">
				<div class="form-group row">
                    <label class="col-sm-3 col-form-label">Message</label>
                    <div class="col-sm-8">
                        <input
                            v-model="form.message"
                            type="text"
                            class="form-control"
                            id="message"
                            placeholder="Insert a message"
                            :class="{ 'is-invalid': form.errors.has('message') }"
                        />
                        <has-error :form="form" field="message"></has-error>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Notes</label>
                    <div class="col-sm-8">
                        <textarea 
                            class="form-control" rows="3" v-model="form.notes" 
                            :class="{ 'is-invalid': form.errors.has('notes') }" 
                            placeholder="Insert your notes"></textarea>
                            <has-error :form="form" field="notes"></has-error>
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
                    field: "message", key: "b", title: "Message", align: "left",
                    renderBodyCell: ({row, column, rowIndex}, h)=> {
						
						return (<div>
                            <div><strong>{row[column.field]}</strong></div>
                            <div class="text-muted">{row['notes']}</div>
                        </div>);
					},
					sortBy: ""
                },
				{
					field: "created_at",
					key: "d",
					title: "Created At",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = this.$moment(row[column.field]).format("lll")
						return text;
					},
					sortBy: "desc"
				},
				{
					field: "",
					key: "f",
					title: "Action",
					align: "center",
                    // <!--this.$router.push(`brands/${this.brandId}/key-messages/${row['id']}`)-->
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return (
							<div class="btn-group btn-block">
								<button type="button" class="btn btn-warning btn-sm"
                                    data-toggle="modal"
							        data-target="#modal-key-message"
                                    on-click={ ()=>{
                                        this.modal.mode = "edit";
                                        this.modal.title = "Edit";
                                        this.formBinding(row['id']);
                                    } }>
									<i class="fas fa-edit"></i>
								</button>
								<button type="button" class="btn btn-danger btn-sm" on-click={()=> this.deleteRow(row['id'])}> 
									<i class="fas fa-trash"></i>
								</button>
							</div>
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
				message: null,
                notes: null
			}),

            modal: {
                mode: "create",
                title: "Add"
            }

		};
	},
    
    computed: {
		formErrors(){
			return this.form.errors;
		}
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
				const { data } = await this.$axios.get(`brands/${this.brandId}/key-messages`, {
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
				title: 'Sure to delete Key Message?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`brands/${this.brandId}/key-messages/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Message is not deleted', '', 'info');
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

                if (this.modal.mode === 'create') {
                    await this.$axios.post(`brands/${this.brandId}/key-messages`, this.form);
                }

                if (this.modal.mode === 'edit') {
                    await this.$axios.put(`brands/${this.brandId}/key-messages/${this.form.id}`, this.form);
                }

				this.$nuxt.$loading.finish();
                this.isLoading = false;


                // Close modal
				$('#modal-key-message .close').click();

                // Reset form
                this.resetForm();

                // Reload table
                this.getDataFromApi();

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

        onShowCreateModal(){
            this.modal.mode = "create";
            this.modal.title = "Add";

            this.resetForm();
        },

        resetForm(){
            this.form = new Form({
                id: null,
                message: null,
                notes: null
            })
        },

        async formBinding(id){
            this.$nuxt.$loading.start();
            this.isLoading = true;
            try {
                const {data} = await this.$axios.get(`brands/${this.brandId}/key-messages/${id}`);

                if (data.success) {
                    this.form = new Form({
                        id: data.data.id,
                        message: data.data.message,
                        notes: data.data.notes
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
        }

    },

    mounted(){
        this.$nextTick(()=> {
            this.getDataFromApi();
        })
    }
};
</script>

<style>
</style>