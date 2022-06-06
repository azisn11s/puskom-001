<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Territory</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Territory</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Region</label>
								<div class="col-sm-8">
									<!-- :reduce="country => country.label" -->
									<v-select
										:clearable="false"
										@open="onOpenRegions"
										@search="fetchRegionsOptions"
										:options="regions"
										v-model="selectedRegion"
										placeholder="Select a region"
									>
									</v-select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Area</label>
								<div class="col-sm-8">
									<!-- :reduce="country => country.label" -->
									<v-select
										@open="onOpenAreas"
										@search="fetchAreasOptions"
										:options="areas"
										v-model="selectedArea"
										:class="{
											'is-invalid': formErrors.has('area_id'),
										}"
										placeholder="Select an area"
									>
									</v-select>
									<has-error :form="form" field="area_id"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Territory</label>
								<div class="col-sm-8">
									<input
										v-model="form.name"
										type="text"
										class="form-control"
										id="name"
										placeholder="Territory Name"
										:class="{ 'is-invalid': form.errors.has('name') }"
									/>
									<has-error :form="form" field="name"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Alias</label>
								<div class="col-sm-8">
									<input
										v-model="form.alias"
										type="text"
										class="form-control"
										placeholder="Alias"
										:class="{
											'is-invalid': form.errors.has('alias'),
										}"
									/>
									<has-error :form="form" field="alias"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Team</label>
								<div class="col-sm-8">
									<v-select
										@open="onOpenTeams"
										@search="fetchTeamsOptions"
										:options="teams"
										v-model="selectedTeam"
										:class="{
											'is-invalid': formErrors.has('team_id'),
										}"
										placeholder="Select a team"
									>
									</v-select>
									<has-error :form="form" field="team_id"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Submit </my-button>
							<nuxt-link
								tag="a"
								to="/territories"
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
				area_id: "",
				name: "",
				alias: "",
				team_id: "",
				employee_id: ""
			}),
			regions: [],
			areas: [],
			teams: [],
			selectedRegion: null,
			selectedArea: null,
			selectedTeam: null,
			selectedEmployee: null,
			isLoading: false,
			employees: []
		};
	},

	methods: {
		async create() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.post(`territories`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/territories`);
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

		fetchRegionsOptions(search, loading) {
			this.$axios
				.get(`search-regions`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.regions = resp.data.items;
					}
				});
		},

		onOpenRegions() {
			this.$axios.get(`search-regions`).then((resp) => {
				if (resp.data.items) {
					this.regions = resp.data.items;
				}
			});
		},

		fetchAreasOptions(search, loading) {
			this.$axios
				.get(`search-areas`, {
					params: {
						region_id: this.selectedRegion
							? this.selectedRegion.code
							: null,
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.areas = resp.data.items;
					}
				});
		},

		onOpenAreas() {
			this.$axios
				.get(`search-areas`, {
					params: {
						region_id: this.selectedRegion
							? this.selectedRegion.code
							: null,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.areas = resp.data.items;
					}
				});
		},

		fetchTeamsOptions(search, loading) {
			this.$axios
				.get(`search-teams`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.teams = resp.data.items;
					}
				});
		},

		onOpenTeams() {
			this.$axios.get(`search-teams`).then((resp) => {
				if (resp.data.items) {
					this.teams = resp.data.items;
				}
			});
		},

		fetchEmployeeOptions(search, loading) {
			this.$axios
				.get(`search-employee`, {
					params: {
						employee_type: 'MR',
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
					employee_type: 'MR'
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
		selectedRegion: function (newVal, oldVal) {
			if (newVal) {
				this.areas = [];
				this.selectedArea = null;
				this.form.area_id = null;
			}
		},
		selectedArea: function (newVal, oldVal) {
			if (newVal) {
				this.form.area_id = newVal.code;
			}
		},
		selectedTeam: function (newVal, oldVal) {
			if (newVal) {
				this.form.team_id = newVal.code;
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
};
</script>

<style>
</style>