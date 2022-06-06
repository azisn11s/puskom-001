<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit Team - #{{ team.id || '-' }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Team</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Team</label>
								<div class="col-sm-8">
									<input
										v-model="form.name"
										type="text"
										class="form-control"
										id="name"
										placeholder="Team name"
										:class="{ 'is-invalid': form.errors.has('name') }"
									/>
									<has-error :form="form" field="name"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Status</label>
								<div class="col-sm-8">
									<input
										type="checkbox"
										v-model="form.is_active"
										class="form-check-input"
										id="is_active"
									/>
									<label class="form-check-label" for="is_active"
										>Active</label
									>
									<has-error :form="form" field="is_active"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<button type="submit" class="btn btn-info">Update</button>
							<nuxt-link
								tag="a"
								to="/teams"
								class="btn btn-default float-right"
							>
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>
		</div>

		<div class="row">

			<!-- Brands -->
			<div class="col-6">
				<div class="card card-default">
					<div class="card-header">
						<h3 class="card-title">Associated Brand</h3>
						<div class="card-tools">
							<button class="btn btn-block btn-outline-primary" data-toggle="modal" data-target="#modal-team-brand">
								<span>Add</span>
							</button>
						</div>
					</div>
					<div class="card-body">
						<ve-table :columns="brand.columns" :table-data="brand.tableData" id="table-container" :sort-option="brand.sortOption" />
					</div>
					<div class="card-footer">
						<div class="table-pagination">
                            <!-- 'sizer', 'jumper' -->
							<ve-pagination
								:total="brand.totalCount"
								:page-index="brand.pageIndex"
								:page-size="brand.pageSize"
								@on-page-number-change="pageNumberChangeBrand"
								@on-page-size-change="pageSizeChangeBrand"
								:layout="['total', 'prev', 'pager', 'next']"
							/>
						</div>
					</div>
				</div>
			</div>

		</div>

		<!-- Modal asosiasi dokter -->
		<modal
			:id="`modal-team-brand`"
			:title="`Add a Brand`"
			:submitFn="submitPendaftaranBrand"
			:loading="loading"
		>
			<template :slot="'body'">
				<div class="row">
					<div class="col-12 col-sm-12">
						<div class="form-group">
							<v-select 
								class="form-control-lg"
								@open="onOpenBrands" @search="fetchBrandOptions" 
								:options="brands"
								v-model="selectedBrand"
								:class="{'is-invalid': formBrandErrors.has('brand_id')}"
								placeholder="Select a brand"
								>
							</v-select>
							<has-error :form="form" field="brand_id"></has-error>
						</div>
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

	data(){
		return {
			brands: [],
			selectedBrand: null,
			loading: false,
			formBrand: new Form({
				brand_id: null
			}),

			/**
			 * Table Brands
			 */
			brand : {
				columns: [
					{
						field: "",
						key: "a",
						title: "No.",
						width: 50,
						align: "center",
						renderBodyCell: ({ row, column, rowIndex }, h) => {
							// return ++rowIndex;
							return (this.brand.pageIndex - 1) * this.brand.pageSize + 1 + rowIndex;
						}					
					},
					{
						field: "brand_name",
						key: "b",
						title: "Brand",
						align: "left",
						renderBodyCell: ({row, column, rowIndex}, h)=> {
							if (!row[column.field]) {
								return ('-');
							}

							return (<b>{row[column.field]}</b>);
						},
						sortBy: ""
					},
					{
						field: "is_active",
						key: "e",
						title: "Status",
						align: "center",
						renderBodyCell: ({row, column, rowIndex}, h)=> {
							if (!row[column.field]) {
								return (<i class="text-red">Inactive</i>);
							}

							return (<i class="text-green">Active</i>);
						},
						sortBy: ""
					},
					{
						field: "",
						key: "f",
						title: "Action",
						align: "center",
						renderBodyCell: ({row, column, rowIndex}, h)=> {
							return (
								<div class="btn-group btn-block">
									<button type="button" class="btn btn-danger" on-click={()=> this.deleteRowBrand(row['id'])}> 
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
								this.getDataFromApiBrand();
							});
						}
					},
				},
			},

		}
	},

	computed: {
		formErrors(){
			return this.form.errors;
		},
		formBrandErrors(){
			return this.formBrand.errors;
		}
	},

	async asyncData({ params, $axios }){
		const team = await $axios.get(`teams/${params.id}`).then((resp)=> {
			return resp.data.data;
		});

		return {
			team,
			form : new Form({
				name: team.name,
				is_active: team.is_active
			})			
		}
	},

	methods: {
		async edit(){
			this.$nuxt.$loading.start();
			try {

				await this.$axios.put(`teams/${this.$route.params.id}`, this.form);

				this.$nuxt.$loading.finish();
				this.$router.push(`/teams`);
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

			}
		},

		fetchBrandOptions(search, loading){
			this.$axios.get(`search-brands`, {params: {
				term: search
			}}).then((resp)=> {
				if (resp.data.items) {
					this.brands = resp.data.items
				}
			})
		},

		onOpenBrands() {
			this.$axios.get(`search-brands`).then((resp)=> {
				if (resp.data.items) {
					this.brands = resp.data.items
				}
			})
		},


		async submitPendaftaranBrand(){
			this.$nuxt.$loading.start();
			try {

				await this.$axios.post(`team-brand/${this.$route.params.id}`, this.formBrand);

				this.$nuxt.$loading.finish();
				this.resetFormBrand();
				this.getDataFromApiBrand();
				// this.$router.push(`/teams/${this.$route.params.id}/edit`);

				// Close Modal Poppup
				$('#modal-team-brand').modal('hide');

			} catch (error) {
				console.log('ERROR!!', error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.formBrand.errors.set(error.response.data.errors);
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

			}
		},

		resetFormBrand(){
			this.selectedBrand = null;
			this.formBrand = new Form({
				brand_id: null
			})
		},


		/**
		 * Required methods
		 * Brand's Table
		 */
		// page number change
		pageNumberChangeBrand(pageIndex) {
			this.brand.pageIndex = pageIndex;
            this.getDataFromApiBrand();
		},
		// page size change
		pageSizeChangeBrand(pageSize) {
			this.brand.pageIndex = 1;
			this.brand.pageSize = pageSize;
            this.getDataFromApiBrand();
		},
		// Get table data from API
		async getDataFromApiBrand() {
            // this.loadingInstance.show();
			try {
				const { data } = await this.$axios.get(`team-brand/${this.$route.params.id}`, {
					params: {
						page: this.brand.pageIndex,
						per_page: this.brand.pageSize,
						search: this.brand.searchFilter,
						sort: this.brand.sortParams
					},
				});

				this.brand.totalCount = data.total;
				this.brand.tableData = data.data;

			} catch (error) {
				console.log("ERROR LIST BRANDS!!!!!!!!!!!", error);
			}
		},
		deleteRowBrand(id){
			this.$swal.fire({
				title: 'Sure to delete associated brand?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`team-brand/${this.$route.params.id}/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApiBrand();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete brand failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Brand is not deleted', '', 'info');
				}
			})

			
		},


	},

	watch: {
		selectedBrand: function (newVal, oldVal) {
			if (newVal) {
				this.formBrand.brand_id = newVal.code
			}
		}
	},

	mounted(){
		this.$nextTick(()=> {
			this.getDataFromApiBrand();
		})
	}
};
</script>

<style>
</style>