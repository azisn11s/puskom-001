<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Region</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Region</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Country</label>
								<div class="col-sm-8">
									<!-- :reduce="country => country.label" -->
									<v-select
										@open="onOpenCountries"
										@search="fetchCountriesOptions"
										:options="countries"
										v-model="selectedCountry"
										:class="{
											'is-invalid': formErrors.has('country_id'),
										}"
										placeholder="Select a country"
									>
									</v-select>
									<has-error
										:form="form"
										field="country_id"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Region</label>
								<div class="col-sm-5">
									<input
										v-model="form.region_name"
										type="text"
										class="form-control"
										placeholder="Region name"
										:class="{
											'is-invalid': form.errors.has('region_name'),
										}"
									/>
									<has-error
										:form="form"
										field="region_name"
									></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Submit </my-button>
							<nuxt-link
								tag="a"
								to="/regions"
								class="btn btn-default float-right"
							>
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
	data() {
		return {
			form: new Form({
				region_name: "",
				country_id: "",
				employee_id: ""
			}),
			countries: [],
			// selectedCountry: null,
			isLoading: false,
			employees: [],
			selectedEmployee: null
		};
	},

	async asyncData({$axios}){
		const selectedCountry = await $axios.get(`search-country`,{
					params: {
						term: 'indonesia',
					},
				}).then((resp) => {
				if (resp.data.items) {
					return resp.data.items[0]
				}
			});

		return {
			selectedCountry
		}
	},

	methods: {
		async create() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.post(`regions`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/regions`);
			} catch (error) {
				console.log("ERROR!!", error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.form.errors.set(error.response.data.errors);
					});

					this.$toast.error(`${error.response.data.message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});
				} else if (error.response && error.response.status == 403) {
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
				this.isLoading = false;

				this.$nuxt.$loading.finish();
			}
		},

		fetchCountriesOptions(search, loading) {
			this.$axios
				.get(`search-country`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.countries = resp.data.items;
					}
				});
		},

		onOpenCountries() {
			this.$axios.get(`search-country`).then((resp) => {
				if (resp.data.items) {
					this.countries = resp.data.items;
				}
			});
		},

		fetchEmployeeOptions(search, loading) {
			this.$axios
				.get(`search-employee`, {
					params: {
						employee_type: 'SLM',
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
					employee_type: 'SLM'
				}
			}).then((resp) => {
				if (resp.data.items) {
					this.employees = resp.data.items;
				}
			});
		},
	},

	computed: {
		formErrors() {
			return this.form.errors;
		},
	},

	watch: {
		selectedCountry: function (newVal, oldVal) {
			if (newVal) {
				this.form.country_id = newVal.code;
			}
		},
		selectedEmployee: function (newVal, oldVal) {
			if (newVal) {
				this.form.employee_id = newVal.code;
			}
			if (!newVal) {
				this.form.employee_id = null;
			}
		},
	},

	mounted(){
		this.$nextTick(()=> {
			this.form.country_id = this.selectedCountry.code
		})
	}
};
</script>

<style>
</style>