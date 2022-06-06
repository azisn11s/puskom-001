<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Doctor</span>
		</portal>

		<div class="row">
			<div class="col-md-7">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Doctor</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Doctor</label>
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
								<label class="col-sm-3 col-form-label">Title</label>
								<div class="col-sm-4">
									<input
										v-model="form.front_title"
										type="text"
										class="form-control"
										id="front_title"
										placeholder="Front Title"
										:class="{
											'is-invalid': form.errors.has('front_title'),
										}"
									/>
									<has-error
										:form="form"
										field="front_title"
									></has-error>
								</div>
								<div class="col-sm-4">
									<input
										v-model="form.back_title"
										type="text"
										class="form-control"
										id="back_title"
										placeholder="Back Title"
										:class="{
											'is-invalid': form.errors.has('back_title'),
										}"
									/>
									<has-error
										:form="form"
										field="back_title"
									></has-error>
								</div>
							</div>

							<!-- Identity ID -->
							<!-- <div class="form-group row">
								<label class="col-sm-3 col-form-label">Identity ID</label>
								<div class="col-sm-4">
									<input
										v-model="form.identity_id"
										type="text"
										class="form-control"
										id="identity_id"
										placeholder="Identity ID"
										:class="{
											'is-invalid': form.errors.has('identity_id'),
										}"
									/>
									<has-error
										:form="form"
										field="identity_id"
									></has-error>
								</div>
							</div> -->

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
								<label class="col-sm-3 col-form-label">Specialty</label>
								<div class="col-sm-8">
									<!-- :reduce="country => country.label" -->
									<v-select
										@open="onOpenSpecialties"
										@search="fetchSpecialtiesOptions"
										:options="specialties"
										v-model="selectedSpecialty"
										:class="{
											'is-invalid': formErrors.has('specialty_id'),
										}"
										placeholder="Select a specialty"
									>
									</v-select>
									<has-error
										:form="form"
										field="specialty_id"
									></has-error>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Classification</label
								>
								<div class="col-sm-8">
									<v-select
										@open="onOpenClassifications"
										@search="fetchClassificationsOptions"
										:options="classifications"
										v-model="selectedClassification"
										:class="{
											'is-invalid':
												formErrors.has('classification_id'),
										}"
										placeholder="Select a classification"
									>
									</v-select>
									<has-error
										:form="form"
										field="classification_id"
									></has-error>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Current Hospital</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.current_hospital"
										type="text"
										class="form-control"
										placeholder="Hospital Name"
										:class="{
											'is-invalid':
												form.errors.has('current_hospital'),
										}"
									/>
									<has-error
										:form="form"
										field="current_hospital"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>City</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.city"
										type="text"
										class="form-control"
										placeholder="Current City"
										:class="{
											'is-invalid':
												form.errors.has('city'),
										}"
									/>
									<has-error
										:form="form"
										field="city"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Address</label
								>
								<div class="col-sm-8">
									<textarea v-model="form.address" class="form-control" placeholder="Full Address"></textarea>
									<has-error
										:form="form"
										field="address"
									></has-error>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Market</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.market"
										type="text"
										class="form-control"
										placeholder="Market"
										:class="{
											'is-invalid':
												form.errors.has('market'),
										}"
									/>
									<has-error
										:form="form"
										field="market"
									></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Submit </my-button>
							<nuxt-link
								tag="a"
								to="/doctors"
								class="btn btn-default float-right"
							>
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>
			<div class="col-md-5">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-info">
							<div class="card-header">
								<h3 class="card-title">Territories</h3>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12 col-sm-12">
										<div class="form-group">
											<label>Associated Territories</label>
											<!-- :reduce="company => company.code" -->
											<v-select
												multiple
												@open="onOpenTerritories"
												@search="fetchTerritoryOptions"
												:options="territories"
												v-model="selectedTerritories"
												:class="{
													'is-invalid':
														formErrors.has('territories'),
												}"
												class="form-control-lg"
												placeholder="Select territories"
											>
											</v-select>
											<has-error
												:form="form"
												field="territories"
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
				identity_id: "",
				first_name: "",
				last_name: "",
				front_title: "",
				back_title: "",
				gender: "",
				current_hospital: "",
				specialty_id: "",
				classification_id: "",
				territories: "",
				city: "",
				address: "",
				market: "",
			}),
			specialties: [],
			classifications: [],
			territories: [],
			selectedSpecialty: null,
			selectedClassification: null,
			selectedTerritories: null,
			isLoading: false,
		};
	},

	methods: {
		async create() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.post(`doctors`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/doctors`);
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

		fetchSpecialtiesOptions(search, loading) {
			this.$axios
				.get(`search-specialty`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.specialties = resp.data.items;
					}
				});
		},

		onOpenSpecialties() {
			this.$axios.get(`search-specialty`).then((resp) => {
				if (resp.data.items) {
					this.specialties = resp.data.items;
				}
			});
		},

		fetchClassificationsOptions(search, loading) {
			this.$axios
				.get(`search-classification`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.classifications = resp.data.items;
					}
				});
		},

		onOpenClassifications() {
			this.$axios.get(`search-classification`).then((resp) => {
				if (resp.data.items) {
					this.classifications = resp.data.items;
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
	},

	computed: {
		formErrors() {
			return this.form.errors;
		},
	},

	watch: {
		selectedSpecialty: function (newVal, oldVal) {
			if (newVal) {
				this.form.specialty_id = newVal.code;
			}
		},
		selectedClassification: function (newVal, oldVal) {
			if (newVal) {
				this.form.classification_id = newVal.code;
			}
		},
		selectedTerritories: function (newVal, oldVal) {
			if (newVal) {
				this.form.territories = newVal;
			}
		},
	},
};
</script>

<style>
</style>