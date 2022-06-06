<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Employee</span>
		</portal>

		<div class="row">
			<div class="col-md-8">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Employee</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Employee</label>
								<div class="col-sm-4">
									<input
										v-model="form.first_name"
										type="text"
										class="form-control"
										id="name"
										placeholder="First Name"
										:class="{
											'is-invalid': form.errors.has('first_name'),
										}"
									/>
									<has-error
										:form="form"
										field="first_name"
									></has-error>
								</div>
								<div class="col-sm-4">
									<input
										v-model="form.last_name"
										type="text"
										class="form-control"
										id="name"
										placeholder="Last Name"
										:class="{
											'is-invalid': form.errors.has('last_name'),
										}"
									/>
									<has-error
										:form="form"
										field="last_name"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Emp. Code</label>
								<div class="col-sm-4">
									<input
										v-model="form.employee_code"
										type="text"
										class="form-control"
										placeholder="Employee Code"
										:class="{
											'is-invalid': form.errors.has('employee_code'),
										}"
									/>
									<has-error
										:form="form"
										field="employee_code"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Date of Birth</label
								>
								<div class="col-sm-4">
									<date-picker
										name="birthdate"
										:input-class="{
											'form-control': true,
											'is-invalid': formErrors.has('birthdate'),
										}"
										placeholder="Click to select date"
										format="d MMMM yyyy"
										minimum-view="day"
										maximum-view="year"
										:monday-first="true"
										v-model="form.birthdate"
										:typeable="false"
										:clear-button="true"
										clear-button-icon="fa fa-times"
										:bootstrap-styling="true"
									>
										<has-error
											slot="afterDateInput"
											:form="form"
											field="birthdate"
										></has-error>
									</date-picker>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Gender</label>
								<div class="col-sm-4">
									<div
										class="custom-control custom-radio"
										:class="{
											'is-invalid': formErrors.has('gender'),
										}"
									>
										<input
											v-model="form.gender"
											value="M"
											class="
												custom-control-input
												custom-control-input-primary
												custom-control-input-outline
											"
											type="radio"
											id="gender-male"
											name="gender"
										/>
										<label
											for="gender-male"
											class="custom-control-label"
										>
											Male</label
										>
									</div>
									<div
										class="custom-control custom-radio"
										:class="{
											'is-invalid': formErrors.has('gender'),
										}"
									>
										<input
											v-model="form.gender"
											value="F"
											class="
												custom-control-input
												custom-control-input-primary
												custom-control-input-outline
											"
											type="radio"
											id="gender-female"
											name="gender"
										/>
										<label
											for="gender-female"
											class="custom-control-label"
										>
											Female</label
										>
									</div>
									<has-error :form="form" field="gender"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Join Date</label>
								<div class="col-sm-4">
									<date-picker
										name="join_date"
										:input-class="{
											'form-control': true,
											'is-invalid': formErrors.has('join_date'),
										}"
										placeholder="Click to select date"
										format="d MMMM yyyy"
										minimum-view="day"
										maximum-view="year"
										:monday-first="true"
										v-model="form.join_date"
										:typeable="false"
										:clear-button="true"
										clear-button-icon="fa fa-times"
										:bootstrap-styling="true"
									>
										<has-error
											slot="afterDateInput"
											:form="form"
											field="join_date"
										></has-error>
									</date-picker>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Employee Type</label
								>
								<div class="col-sm-4">
									<v-select
										@open="onOpenEmpTypes"
										@search="fetchEmpTypesOptions"
										:options="employeeTypes"
										v-model="selectedEmpType"
										:class="{
											'is-invalid':
												formErrors.has('employee_type_id'),
										}"
										placeholder="Select a employee type"
									>
									</v-select>
									<has-error
										:form="form"
										field="employee_type_id"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Email</label>
								<div class="col-sm-6">
									<input
										v-model="form.email"
										type="text"
										class="form-control"
										placeholder="Email Address"
										:class="{
											'is-invalid': form.errors.has('email'),
										}"
									/>
									<has-error :form="form" field="email"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Mobile Phone</label
								>
								<div class="col-sm-6">
									<input
										v-model="form.mobile_number"
										type="text"
										class="form-control"
										placeholder="Mobile Phone Number"
										:class="{
											'is-invalid': form.errors.has('mobile_number'),
										}"
									/>
									<has-error
										:form="form"
										field="mobile_number"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Address</label>
								<div class="col-sm-8">
									<textarea
										class="form-control"
										v-model="form.address"
										placeholder="Full Address"
									></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Zip Code</label>
								<div class="col-sm-4">
									<input
										v-model="form.zip_code"
										type="text"
										class="form-control"
										placeholder="Zip Code or Postal Code"
										:class="{
											'is-invalid': form.errors.has('zip_code'),
										}"
									/>
									<has-error :form="form" field="zip_code"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Submit </my-button>
							<nuxt-link
								tag="a"
								to="/employees"
								class="btn btn-default float-right"
							>
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Company & Role</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Associated Company</label>
											<!-- <input class="form-control form-control-border border-width-2" :disabled="true" :value="selectedCompany ? selectedCompany[0].label : '-'" /> -->

											<v-select
												@open="onOpenCompanies"
												@search="fetchCompanyOptions"
												:options="companies"
												v-model="selectedCompany"
												:class="{
													'is-invalid':
														formErrors.has('company_id'),
												}"
												placeholder="Select a company"
											>
											</v-select>
											<has-error
												:form="form"
												field="company_id"
											></has-error>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Territory</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Territory</label>
											<input class="form-control form-control-border border-width-2" :disabled="true" :value="selectedTerritory ? selectedTerritory[0].label : '-'" />

											<!-- <v-select
												@open="onOpenTerritories"
												@search="fetchTerritoryOptions"
												:options="territories"
												v-model="selectedTerritory"
												:class="{
													'is-invalid':
														formErrors.has('territory_id'),
												}"
												placeholder="Select a territory"
												:disabled="true"

											>
											</v-select> -->
											<has-error
												:form="form"
												field="territory_id"
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
				employee_code: "",
				first_name: "",
				last_name: "",
				birthdate: "",
				gender: "",
				join_date: "",
				address: "",
				email: "",
				mobile_number: "",
				company_id: "",
				territory_id: "",
				employee_type_id: "",
			}),
			companies: [],
			territories: [],
			employeeTypes: [],
			selectedCompany: null,
			selectedTerritory: null,
			selectedEmpType: null,
			isLoading: false,
			// token: this.$auth.strategy.token.get()
		};
	},

	methods: {
		async create() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.post(`employees`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.go(-1);
				// this.$router.push(`/employees`);
			} catch (error) {
				// console.log("ERROR!!", error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.form.errors.set(error.response.data.errors);
					});

					this.$toast.error(`${error.response.data.message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
						theme: 'outline',
						position: 'top-center',
						action : {
							text : 'Close',
							onClick : (e, toastObject) => {
								toastObject.goAway(0);
							}
						}
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

		fetchCompanyOptions(search, loading) {
			this.$axios
				.get(`search-company`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.companies = resp.data.items;
					}
				});
		},

		onOpenCompanies() {
			this.$axios.get(`search-company`).then((resp) => {
				if (resp.data.items) {
					this.companies = resp.data.items;
				}
			});
		},

		fetchTerritoryOptions(search, loading) {
			this.$axios
				.get(`search-territories`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.territories = resp.data.items;
					}
				});
		},

		onOpenTerritories() {
			this.$axios.get(`search-territories`).then((resp) => {
				if (resp.data.items) {
					this.territories = resp.data.items;
				}
			});
		},

		fetchEmpTypesOptions(search, loading) {
			this.$axios
				.employeeTypes(`search-employee-type`, {
					params: {
						term: EmployeeType,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.employeeTypes = resp.data.items;
					}
				});
		},

		onOpenEmpTypes() {
			this.$axios.get(`search-employee-type`).then((resp) => {
				if (resp.data.items) {
					this.employeeTypes = resp.data.items;
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
		selectedCompany: function (newVal, oldVal) {
			if (newVal) {
				this.form.company_id = newVal.code;
			}
		},

		selectedTerritory: function (newVal, oldVal) {
			if (newVal) {
				this.form.territory_id = newVal.code;
			}
		},

		selectedEmpType: function (newVal, oldVal) {
			if (newVal) {
				this.form.employee_type_id = newVal.code;
			}
		},
	},
};
</script>

<style>
</style>