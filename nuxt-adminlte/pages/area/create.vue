<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Area</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Area</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Region</label
								>
								<div class="col-sm-8">
									<!-- :reduce="country => country.label" -->
									<v-select 
										@open="onOpenRegions" @search="fetchRegionsOptions" 
										:options="regions"
										v-model="selectedRegion"
										:class="{'is-invalid': formErrors.has('region_id')}"
										placeholder="Select a region"
										>
									</v-select>
									<has-error :form="form" field="region_id"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Area</label
								>
								<div class="col-sm-5">
									<input
										v-model="form.area_name"
										type="text"
										class="form-control"
										placeholder="Area name"
										:class="{ 'is-invalid': form.errors.has('area_name') }"
									/>
									<has-error :form="form" field="area_name"></has-error>
								</div>
							</div>
							
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<MyButton :loading="isLoading">Submit</MyButton>
							<nuxt-link tag="a" to="/areas" class="btn btn-default float-right">
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>

			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Associated Employee</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Employee (PIC)</label>
											<v-select
												@open="onOpenEmployees"
												@search="fetchEmployeeOptions"
												:filterable="false"
												:options="employees"
												v-model="selectedEmployee"
												:class="{
													'is-invalid': formErrors.has('employee_id')
												}"
												placeholder="Select an employee"
											>
												<template v-slot:option="option">
													<div><strong>{{ option.label }}</strong></div> 
													<div class="text-muted">(Emp. Code {{ option.employee_code }}) at {{ option.company_name }}</div>
												</template>
												<template #selected-option="{ label, company_name }">
													<div style="display: flex; align-items: baseline">
														<strong>{{ label }} </strong>
														<em style="margin-left: 0.5rem">at {{ company_name }}</em>
													</div>
												</template>
											</v-select>
											<has-error
												:form="form"
												field="employee_id"
											></has-error>
										</div>
									</div>									
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</template>

<script>
import Form from "vform";

export default {
	data(){
		return {
			form: new Form({
				area_name: '',
				region_id: '',
				employee_id: ''
			}),
			regions: [],
			selectedRegion: null,
			isLoading: false,
			employees: [],
			selectedEmployee: null
		}
	},

	methods: {
		async create(){
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {

				await this.$axios.post(`areas`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;
				this.$router.push(`/areas`);
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

		fetchRegionsOptions(search, loading){
			this.$axios.get(`search-regions`, {params: {
				term: search
			}}).then((resp)=> {
				if (resp.data.items) {
					this.regions = resp.data.items
				}
			})
		},

		onOpenRegions() {
			this.$axios.get(`search-regions`).then((resp)=> {
				if (resp.data.items) {
					this.regions = resp.data.items
				}
			})
		},

		fetchEmployeeOptions(search, loading) {
			this.$axios
				.get(`search-employee`, {
					params: {
						employee_type: 'AR',
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.employees = resp.data.items;
					}
				});
		},

		onOpenEmployees() {
			this.$axios.get(`search-employee`, {
				params: {
					employee_type: 'AR'
				}
			}).then((resp) => {
				if (resp.data.items) {
					this.employees = resp.data.items;
				}
			});
		},
	},

	computed: {
		formErrors(){
			return this.form.errors;
		}
	},

	watch: {
		selectedRegion: function (newVal, oldVal) {
			if (newVal) {
				this.form.region_id = newVal.code
			}
		},
		selectedEmployee: function (newVal, oldVal) {
			if (newVal) {
				this.form.employee_id = newVal.code
			}
			if (!newVal) {
				this.form.employee_id = null;
			}
		}
	}
};
</script>

<style>
</style>