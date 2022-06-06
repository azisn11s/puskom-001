<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit Territory - #{{ form.alias || form.id }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Territory</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
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
								<label class="col-sm-3 col-form-label">Code</label>
								<div class="col-sm-8">
									<input
										v-model="territory.code"
										type="text"
										class="form-control form-control-border border-width-2"
										id="code"
										disabled
									/>
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
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Status</label>
								<div class="col-sm-8">
									<input
										type="checkbox"
										v-model="form.status"
										class="form-check-input"
										id="status"
									/>
									<label class="form-check-label" for="status"
										>Active</label
									>
									<has-error :form="form" field="status"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Update </my-button>
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
import Modal from "~/components/Modal";

export default {

	data() {
		return {
			regions: [],
			areas: [],
			isLoading: false,
			employees: [],
			teams: [],
			selectedTeam: null,
			loading: false,
		};
	},

	async asyncData({ params, $axios }) {
		const territory = await $axios
			.get(`territories/${params.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		const selectedArea = await $axios
			.get(`areas/${territory.area_id}`)
			.then((resp) => {
				return {
					code: resp.data.data.id,
					label: resp.data.data.area_name,
					region_id: resp.data.data.region_id,
				};
			});

		const selectedRegion = await $axios
			.get(`regions/${selectedArea.region_id}`)
			.then((resp) => {
				return {
					code: resp.data.data.id,
					label: resp.data.data.region_name,
				};
			});

		const selectedTeam = (!territory.team_id) ? null : await $axios
			.get(`teams/${territory.team_id}`)
			.then((resp) => {
				return {
					code: resp.data.data.id,
					label: resp.data.data.name,
				};
			});

		const selectedEmployee = (!territory.employee_id) ? null : await $axios
			.get(`employees/${territory.employee_id}`)
			.then((resp) => {
				return {
					code: resp.data.data.id,
					label: `${resp.data.data.first_name} ${resp.data.data.last_name}`,
					employee_code: resp.data.data.employee_code,
					company_name: resp.data.data.company ? resp.data.data.company.name : "" 
				};
			}).catch(err=> null);

		return {
			form: new Form({
				area_id: territory.area_id,
				name: territory.name,
				alias: territory.alias,
				status: territory.status,
				team_id: territory.team_id,
			}),
			selectedRegion,
			selectedArea,
			selectedEmployee,
			selectedTeam,
			territory
		};
	},

	methods: {
		async edit() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.put(
					`territories/${this.$route.params.id}`,
					{...this.form, employee_id: this.selectedEmployee ? this.selectedEmployee.code : ''}
				);

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


		fetchTeamOptions(search, loading){
			this.$axios.get(`search-teams`, {params: {
				term: search
			}}).then((resp)=> {
				if (resp.data.items) {
					this.teams = resp.data.items
				}
			})
		},

		onOpenTeams() {
			this.$axios.get(`search-teams`).then((resp)=> {
				if (resp.data.items) {
					this.teams = resp.data.items
				}
			})
		},

	},

	computed: {
		formErrors() {
			return this.form.errors;
		}
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