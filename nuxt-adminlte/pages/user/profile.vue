<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>User Profile</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">User</h3>
					</div>

					<form @submit.prevent="editUser" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label for="fullname" class="col-sm-3 col-form-label"
									>Fullname</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.fullname"
										type="text"
										class="form-control"
										id="fullname"
										placeholder="Fullname"
										:class="{
											'is-invalid': form.errors.has('fullname'),
										}"
									/>
									<has-error :form="form" field="fullname"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label for="email" class="col-sm-3 col-form-label"
									>Email</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.email"
										type="email"
										class="form-control"
										id="inputEmail3"
										placeholder="Email"
										:class="{
											'is-invalid': form.errors.has('email'),
										}"
									/>
									<has-error :form="form" field="email"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label for="role" class="col-sm-3 col-form-label"
									>Role</label
								>
								<div class="col-sm-8">
									<input
										v-model="userDetail.roles[0].name"
										type="role"
										class="form-control"
										id="role"
										disabled
									/>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<button type="submit" class="btn btn-info">Update</button>
							<nuxt-link
								tag="a"
								to="/"
								class="btn btn-default float-right"
							>
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>

			<!-- Change Password -->
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Change Password</h3>
					</div>

					<form @submit.prevent="changePassword" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label
									for="current_password"
									class="col-sm-4 col-form-label"
									>Current Password</label
								>
								<div class="col-sm-8">
									<input
										v-model="formChangePassword.current_password"
										type="password"
										class="form-control"
										id="current_password"
										placeholder="Current Password"
										:class="{
											'is-invalid':
												formChangePassword.errors.has(
													'current_password'
												),
										}"
									/>
									<has-error
										:form="formChangePassword"
										field="current_password"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label
									for="new_password"
									class="col-sm-4 col-form-label"
									>New Password</label
								>
								<div class="col-sm-8">
									<input
										v-model="formChangePassword.new_password"
										type="password"
										class="form-control"
										id="new_password"
										placeholder="New Password"
										:class="{
											'is-invalid':
												formChangePassword.errors.has(
													'new_password'
												),
										}"
									/>
									<has-error
										:form="formChangePassword"
										field="new_password"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label
									for="new_password_confirmation"
									class="col-sm-4 col-form-label"
									>Passw. Confirmation</label
								>
								<div class="col-sm-8">
									<input
										v-model="
											formChangePassword.new_password_confirmation
										"
										type="password"
										class="form-control"
										id="new_password_confirmation"
										placeholder="Re-type New Password"
										:class="{
											'is-invalid': formChangePassword.errors.has(
												'new_password_confirmation'
											),
										}"
									/>
									<has-error
										:form="formChangePassword"
										field="new_password_confirmation"
									></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<button type="submit" class="btn btn-info">Submit</button>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>
		</div>

		<div class="row" v-if="userDetail.employee">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-text-width"></i>
							As Employee at <strong>{{ userDetail.employee.company.name }}</strong>
						</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<dl class="row">
							<dt class="col-sm-4">Fullname</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.first_name}} {{userDetail.employee.last_name}}
							</dd>
							<dt class="col-sm-4">Employee Code</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.employee_code}}
							</dd>
							<dt class="col-sm-4">Birthdate</dt>
							<dd class="col-sm-8">
								{{$moment(userDetail.employee.birthdate).format('ll')}}
							</dd>
							<dt class="col-sm-4">Gender</dt>
							<dd class="col-sm-8">
								<span v-if="userDetail.employee.gender == 'M'">Male</span>
								<span v-if="userDetail.employee.gender == 'F'">Female</span>
							</dd>
							<dt class="col-sm-4">Join Date</dt>
							<dd class="col-sm-8">
								{{$moment(userDetail.employee.join_date).format('ll')}}
							</dd>
							<dt class="col-sm-4">Employee Type</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.employee_type.name}}
							</dd>
							<dt class="col-sm-4">Employee Status</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.employee_status.status}}
							</dd>
							<dt class="col-sm-4">Email</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.email}}
							</dd>
							<dt class="col-sm-4">Mobile Number</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.mobile_number}}
							</dd>
							<dt class="col-sm-4">Address</dt>
							<dd class="col-sm-8">
								{{userDetail.employee.address || '-'}}
							</dd>
							
						</dl>
					</div>
					<!-- /.card-body -->
				</div>
				<!-- /.card -->
			</div>

			<!-- Maps Current Location -->
			<section class="col-lg-6">
				<!-- Custom tabs (Charts with tabs)-->
				<div class="card" style="position: relative; left: 0px; top: 0px">
					<div class="card-header ui-sortable-handle" style="cursor: move">
						<h3 class="card-title">
							<i class="fas fa-map-marked mr-1"></i>
							Your Location
						</h3>
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<div class="tab-content p-0">
							<vue-element-loading
								:active="loading.geolocation"
								:is-full-screen="false"
							/>
							<div
								id="map-wrap"
								style="height: 40vh"
								v-if="currentLocation.lat && currentLocation.lng && !loading.geolocation"
							>
								<client-only>
									<l-map
										:zoom="16"
										:center="[
											currentLocation.lat,
											currentLocation.lng,
										]"
									>
										<l-tile-layer
											url="http://{s}.tile.osm.org/{z}/{x}/{y}.png"
										></l-tile-layer>
										<l-marker
											:lat-lng="[
												currentLocation.lat,
												currentLocation.lng,
											]"
										></l-marker>
									</l-map>
								</client-only>
							</div>
							<p v-if="errorCurrentLocation">{{errorCurrentLocation}}</p>
						</div>
					</div>
					<!-- /.card-body -->
				</div>
				<!-- /.card -->
			</section>
		</div>
	</div>
</template>

<script>
import Form from "vform";
export default {
	data() {
		return {
			loading: {
				geolocation: false
			},
			currentLocation: {},
			errorCurrentLocation: ""
		};
	},

	computed: {
		currentUser() {
			return this.$auth.user;
		},

		formErrors() {
			return this.form.errors;
		},

		formErrorsChangePassword() {
			return this.formChangePassword.errors;
		},
	},

	async asyncData({ params, $axios, $auth }) {
		const userDetail = await $axios
			.get(`admin/users/${$auth.user.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		return {
			userDetail,
			form: new Form({
				fullname: userDetail.name,
				email: userDetail.email,
				password: "",
				password_confirmation: "",
			}),
			formChangePassword: new Form({
				current_password: "",
				new_password: "",
				new_password_confirmation: "",
			}),
		};
	},

	methods: {
		async editUser() {
			this.$nuxt.$loading.start();
			try {
				await this.$axios.put(
					`admin/users/${this.$auth.user.id}`,
					this.form
				);

				this.$swal.fire("User record updated successfully!", "", "success");

				this.$nuxt.$loading.finish();
				// this.$router.push({name: 'user.profile'});
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

				this.$nuxt.$loading.finish();
			}
		},

		async changePassword() {
			this.$nuxt.$loading.start();
			try {
				await this.$axios.put(
					`admin/users/${this.$auth.user.id}/change-password`,
					this.formChangePassword
				);

				this.$swal.fire(
					"User password updated successfully!",
					"",
					"success"
				);

				this.$nuxt.$loading.finish();
				// this.$router.push({name: 'user.profile'});
			} catch (error) {
				console.log("ERROR!!", error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.formChangePassword.errors.set(
							error.response.data.errors
						);
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

				this.$nuxt.$loading.finish();
			}
		},

		getCurrentLocation() {
			this.loading.geolocation = true;
			this.$watchLocation({})
				.then((coordinates) => {
					this.currentLocation = coordinates;
					this.loading.geolocation = false;
				})
				.catch((error) => {
					console.log("Alert from Geolocation", error);
					this.errorCurrentLocation = `${error}. Make sure to enable browser location permission.`;
					this.loading.geolocation = false;

					// this.$toast.error("Alert from Geolocation", error);
				});
		},
	},

	created() {
		this.getCurrentLocation();
	},
};
</script>

<style>
</style>