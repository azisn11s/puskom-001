<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit User</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">User</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="editUser" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label for="inputEmail3" class="col-sm-3 col-form-label"
									>Username</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.username"
										type="text"
										class="form-control"
										id="username"
										placeholder="username" 
										:class="{ 'is-invalid': form.errors.has('username') }"
									/>
									<has-error :form="form" field="username"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label for="inputEmail3" class="col-sm-3 col-form-label"
									>Email</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.email"
										type="email"
										class="form-control"
										id="inputEmail3"
										placeholder="Email"
										:class="{ 'is-invalid': form.errors.has('email') }"
									/>
									<has-error :form="form" field="email"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label
									for="inputPassword3"
									class="col-sm-3 col-form-label"
									>Password</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.password"
										type="password"
										class="form-control"
										id="inputPassword3"
										placeholder="Password"
										:class="{ 'is-invalid': form.errors.has('password') }"
									/>
									<has-error :form="form" field="password"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label
									for="password_confirmation"
									class="col-sm-3 col-form-label"
									>Password Confirmation</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.password_confirmation"
										type="password"
										class="form-control"
										id="password_confirmation"
										placeholder="Password Confirmation"
										:class="{ 'is-invalid': form.errors.has('password_confirmation') }"
									/>
									<has-error :form="form" field="password_confirmation"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label for="role" class="col-sm-3 col-form-label"
									>Role</label
								>
								<div class="col-sm-8">
									<select class="form-control" id="role" v-model="form.role" 
										:class="{ 'is-invalid': form.errors.has('role') }"
									>
										<option value="">---</option>
										<option v-for="(item, index) in listOfRoles.data" :key="index" :value="item.slug">
											{{ item.name }}
										</option>
									</select>
									<has-error :form="form" field="role"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<button type="submit" class="btn btn-info">Submit</button>
							<nuxt-link tag="a" to="/user" class="btn btn-default float-right">
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>


			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">User Detail</h3>
					</div>
						<div class="card-body">

							<div class="row">
								<div class="col-12 col-sm-12">
									<div class="form-group">
										<label>Identity Number</label>
										<input
											v-model="form.identity_number"
											type="text"
											class="form-control"
											id="identity_number"
											placeholder="Identity Number"
											:class="{ 'is-invalid': form.errors.has('identity_number') }"
										/>
										<has-error :form="form" field="identity_number"></has-error>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>First Name</label>
										<input
											v-model="form.first_name"
											type="text"
											class="form-control"
											id="first_name"
											placeholder="First Name"
											:class="{ 'is-invalid': form.errors.has('first_name') }"
										/>
										<has-error :form="form" field="first_name"></has-error>
									</div>
								</div>
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Last Name</label>
										<input
											v-model="form.last_name"
											type="text"
											class="form-control"
											id="last_name"
											placeholder="Last Name"
											:class="{ 'is-invalid': form.errors.has('last_name') }"
										/>
										<has-error :form="form" field="last_name"></has-error>
									</div>
								</div>

								
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Gender</label>
								<div class="col-sm-4">
									<div
										class="custom-control custom-radio"
										:class="{
											'is-invalid': form.errors.has('gender'),
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
											'is-invalid': form.errors.has('gender'),
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

							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<label 
											>Date of Birth</label
										>
										<date-picker
											name="birth_date"
											:input-class="{
												'form-control': true,
												'is-invalid': form.errors.has('birth_date'),
											}"
											placeholder="Click to select date"
											format="d MMMM yyyy"
											minimum-view="day"
											maximum-view="year"
											:monday-first="true"
											v-model="form.birth_date"
											:typeable="false"
											:clear-button="true"
											clear-button-icon="fa fa-times"
											:bootstrap-styling="true"
										>
											<has-error
												slot="afterDateInput"
												:form="form"
												field="birth_date"
											></has-error>
										</date-picker>
									</div>
								</div>								
							</div>

							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Birth Place</label>
										<input
											v-model="form.birth_place"
											type="text"
											class="form-control"
											id="birth_place"
											placeholder="Birth Place"
											:class="{ 'is-invalid': form.errors.has('birth_place') }"
										/>
										<has-error :form="form" field="birth_place"></has-error>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Religion</label>
										<input
											v-model="form.religion"
											type="text"
											class="form-control"
											id="religion"
											placeholder="Religion"
											:class="{ 'is-invalid': form.errors.has('religion') }"
										/>
										<has-error :form="form" field="religion"></has-error>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Phone Number</label>
										<input
											v-model="form.phone_number"
											type="text"
											class="form-control"
											id="phone_number"
											placeholder="Phone Number"
											:class="{ 'is-invalid': form.errors.has('phone_number') }"
										/>
										<has-error :form="form" field="phone_number"></has-error>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Address</label>
										<textarea class="form-control" v-model="form.full_address"></textarea>
										<has-error :form="form" field="phone_number"></has-error>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-6 col-sm-6">
									<div class="form-group">
										<label>Zip Code</label>
										<input
											v-model="form.zip_code"
											type="text"
											class="form-control"
											id="zip_code"
											placeholder="Zip Code"
											:class="{ 'is-invalid': form.errors.has('zip_code') }"
										/>
										<has-error :form="form" field="zip_code"></has-error>
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
			employees: [],
			modelConfig: {
				type: "string",
				mask: "YYYY-MM-DD", // Uses 'iso' if missing
			},
		}
	},

	async asyncData({ params, $axios }){
		const user = await $axios.get(`admin/users/${params.id}`).then((resp)=> {
			return resp.data.data;
		});

		const userDetail = user.user_detail || {};

		const listOfRoles = await $axios.get(`admin/roles`, {
			params: {
				'list_only': true
			}
		});

		const selectedEmployee = await $axios.get(`admin/users/${user.id}`).then((resp)=> {
			if (resp.data.success) {
				return [{
					code: resp.data.data.id,
					label: `${resp.data.data.first_name} ${resp.data.data.last_name}`
				}]
			}

			return null;
		});

		return {
			listOfRoles,
			form : new Form({
				username: user.username,
				email: user.email,
				role: '',
				password: '',
				password_confirmation: '',
				employee_id: selectedEmployee.length ? selectedEmployee[0].code : '',

				identity_number: userDetail.identity_number,
				first_name: userDetail.first_name,
				last_name: userDetail.last_name,
				gender: userDetail.gender,
				birth_date: userDetail.birth_date,
				birth_place: userDetail.birth_place,
				religion: userDetail.religion,
				phone_number: userDetail.phone_number,
				full_address: userDetail.full_address,
				zip_code: userDetail.zip_code
			}),
			selectedEmployee
		}
	},

	methods: {
		async editUser(){
			this.$nuxt.$loading.start();
			try {

				await this.$axios.put(`admin/users/${this.$route.params.id}`, this.form);

				this.$nuxt.$loading.finish();
				this.$router.push(`/user`);
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

		fetchEmployeeOptions(search, loading){
			this.$axios.get(`search-employee`, {params: {
				term: search
			}}).then((resp)=> {
				if (resp.data.items) {
					this.employees = resp.data.items
				}
			})
		},

		onOpenEmployees() {
			this.$axios.get(`search-employee`).then((resp)=> {
				if (resp.data.items) {
					this.employees = resp.data.items
				}
			})
		}
	},

	computed: {
		formErrors(){
			return this.form.errors;
		}
	},

	watch: {
		selectedEmployee: function (newVal, oldVal) {
			if (newVal) {
				this.form.employee_id = newVal.code
			}
		}
	}

};
</script>

<style>
</style>