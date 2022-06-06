<template>
	<div>
		<portal to="header-title">
			<span>Create - Call Plan at <b>{{ $moment(selectedDate).format('dddd, ll') }}</b></span>
		</portal>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group row">
					<div class="col-sm-8">
						<v-select 
							:options="listActivities"
							v-model="selectedActivity"
							placeholder="Select an activity" 
							:class="{'is-invalid': formErrors.has('activity_id')}"
							>
						</v-select>
						<has-error :form="formData" field="activity_id"></has-error>
					</div>
					<div class="col-sm-2">
						<button type="submit" class="btn btn-primary" :disabled="isLoading" @click.prevent="submit">
							Submit
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Table of doctors -->
		<div class="row">
			<div class="col-lg-12">
				<div class="card">

					<vue-element-loading :active="loadingDoctors" :is-full-screen="false" style="z-index: 1"/>

					<div class="card-header">
						<h3 class="card-title">Assicated Doctors</h3>
					</div>
					<table class="table table-hover">
						<thead>
							<th width="2%">No.</th>
							<th width="5%">
								<div class="form-check d-flex">
								 	<input type="checkbox" class="" :checked="isDoctorsAllChecked" @change="onCheckAllDoctor($event)" />
								</div>
							</th>
							<th width="20%">Doctor</th>
							<th width="10%">Specialty</th>
							<th width="20%">Work with Details</th>
							<th></th>
							<th></th>
							<th></th>
						</thead>
						<tbody>
							<tr v-for="(doctor, idxD) in doctors" :key="idxD">
								<td>{{ idxD + 1}}</td>
								<td>
									<div class="form-check d-flex">
										<input :id="`doctor_${doctor.id}`" type="checkbox" class="" 
											:value="doctor.id" 
											:checked="checkedDoctors.indexOf(parseInt(doctor.id)) > -1"
											@change="onCheckDoctor($event)" />
									</div>
								</td>
								<td>
									<span v-if="doctor.front_title">{{ doctor.front_title}} {{ doctor.first_name }} {{ doctor.last_name }}</span>
									<span v-if="!doctor.front_title">{{ doctor.first_name }} {{ doctor.last_name }}</span>
								</td>
								<td>
									<span v-if="doctor.specialty">{{ doctor.specialty.specialty }}</span>
									<span v-if="!doctor.specialty">-</span>
								</td>
								<td>
									<div class="d-flex">
										<div class="form-check">
											<input :id="`radio_ww_${idxD}_self`" class="form-check-input" type="radio" :name="`radio_ww_${idxD}`" v-model="doctors[idxD].work_with" value="self">
											<label :for="`radio_ww_${idxD}_self`" class="form-check-label">Self</label>
										</div>	
										<div class="form-check pl-5">
											<input :id="`radio_ww_${idxD}_jw`" class="form-check-input" type="radio" :name="`radio_ww_${idxD}`" v-model="doctors[idxD].work_with" value="join_with">
											<label :for="`radio_ww_${idxD}_jw`" class="form-check-label">Join With</label>
										</div>	
									</div>
								</td>
								<td>
									<!-- @search="fetchRegionsOptions" -->
									<v-select 
										v-if="doctor.work_with == 'join_with' && doctor.territories.length"
										multiple 
										@open="onOpenEmployees(doctor.territories[0].id)" 
										:options="employees"
										v-model="doctor.work_with_employees"
										placeholder="Select employees"
										>
									</v-select>
								</td>
								<td></td>
								<td>
									
								</td>
							</tr>
						</tbody>
					</table>
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
			isLoading: false,
			selectedActivity: null,
			formData: new Form({
				// date_of_activity: this.$moment(this.selectedDate).format(''),
				activity_id: '',
				doctors: []
			}),
			listDoctors: [],
			checkedDoctors: [],
			employees: [],
			isDoctorsAllChecked: false,
			doctors: [],
			loadingDoctors: false
		};
	},

	async asyncData({params, $axios, $moment}){
		const listActivities = await $axios.get(`call-plan/activities`).then((resp)=>{
				if (resp.data.success) {
					return resp.data.data.map((item)=> {
						item.code = item.id;
						item.label = item.name;

						return item;
					});
				}

				return []
			})

		// const doctors = await $axios.get(`call-plan/doctors`).then((resp)=> {
		// 	if (resp.data.success) {
		// 		return resp.data.data.map((item)=> {
		// 				item.work_with = 'self';
		// 				item.work_with_employees = [];

		// 				return item;
		// 			});
		// 	}

		// 	return []
		// });

		return {
			selectedDate: params.date,
			listActivities
			// doctors
		}
	},

	computed: {
		formErrors(){
			return this.formData.errors;
		}
	},

	watch: {
		'selectedActivity': function (newVal, oldVal) {
			
			if (newVal.slug === 'field-work') {
				this.getDoctors();
			}

			if (newVal.slug !== 'field-work') {
				this.doctors = [];
			}

			this.formData.activity_id = newVal.id
		},
		'isDoctorsAllChecked': function(newVal){
			if (newVal) {
				this.checkedDoctors = [];
				// this.formData.doctors = [];
				this.doctors.forEach(item=> {
					this.checkedDoctors.push(parseInt(item.id));

					// let doctor = {id: parseInt(item.id), work_with: null }
					// this.formData.doctors.push(doctor);
				});
			}
			if (!newVal) {
				this.checkedDoctors = [];
				// this.formData.doctors = [];
			}
		}
	},

	methods: {
		async submit(){
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				this.formData.date_of_activity = this.selectedDate;

				this.formData.doctors = [];

				await this.checkedDoctors.forEach((item)=> {
					let doctor = this.doctors.filter((doctor)=> doctor.id == item);
					this.formData.doctors.push({id: doctor[0].id, work_with: doctor[0].work_with, work_employees: doctor[0].work_with_employees });
				})

				const { data } = await this.$axios.post(`call-plan/schedule-activities`, this.formData);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$toast.success(`${data.message}`, {
					icon: "check",
					iconPack: "fontawesome",
					duration: 3000,
				});	

				this.$router.push(`/call-plan`);
			} catch (error) {
				console.log('ERROR!!', error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.formData.errors.set(error.response.data.errors);
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

		addRowDoctor(){
			let rowDoctor = {
				doctor: null
			}

			this.formData.doctors.push(rowDoctor);
		},

		fetchDoctorsOptions(search, loading) {
			this.$axios
				.get(`search-doctors`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.listDoctors = resp.data.items;
					}
				});
		},

		onOpenDoctor() {
			this.$axios.get(`search-doctors`).then((resp) => {
				if (resp.data.items) {
					this.listDoctors = resp.data.items;
				}
			});
		},

		onCheckDoctor(e){
			if (e.target.checked && e.target.value) {
				this.checkedDoctors.push(parseInt(e.target.value));

				// let doctor = {id: parseInt(e.target.value), work_with: null }
				// this.formData.doctors.push(doctor);
			}
			if (!e.target.checked && e.target.value) {
				this.checkedDoctors = this.checkedDoctors.filter((item)=> item != parseInt(e.target.value))
				// this.formData.doctors = this.formData.doctors.filter((item)=> item.id != parseInt(e.target.value))
			}
		},

		onCheckAllDoctor(e){
			if (e.target.checked && e.target.value) {
				this.isDoctorsAllChecked = true;
			}
			if (!e.target.checked && e.target.value) {
				this.isDoctorsAllChecked = false;
			}
		},

		fetchEmployeesOptions(search, loading){
			this.$axios.get(`call-plan/employee-hierarchy`, {params: {
				term: search
			}}).then((resp)=> {
				if (resp.data.items) {
					this.regions = resp.data.items
				}
			})
		},

		onOpenEmployees(territoryId) {
			this.$axios.get(`call-plan/employee-hierarchy/${territoryId}`).then((resp)=> {
				if (resp.data.data) {
					this.employees = resp.data.data
				}
			})
		},

		getDoctors(){
			this.loadingDoctors = true;
			this.$axios.get(`call-plan/doctors`).then((resp)=> {
				if (resp.data.success) {
					this.doctors = resp.data.data.map((item)=> {
							item.work_with = 'self';
							item.work_with_employees = [];

							return item;
						});
					
					this.loadingDoctors = false;
				}
			}).catch((err)=>{
				this.loadingDoctors = false;
			});
		 }
	},
};
</script>