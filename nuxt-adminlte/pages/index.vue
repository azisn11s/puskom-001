<template>
	<div class="container-fluid">
		<portal to="header-title">Dashboard</portal>

		<!-- Small Boxes -->
		<div class="row">
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<MyBox
					:color="`purple`"
					:icon="`user-tie`"
					:url="{ name: 'employee.index' }"
					:isLoading="loading.employee"
				>
					<h3>{{ counter.employee }}</h3>
					<p>Employees</p>
				</MyBox>
			</div>
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<MyBox
					:color="`yellow`"
					:icon="`user-md`"
					:url="{ name: 'doctor.index' }"
					:isLoading="loading.doctor"
				>
					<h3>{{ counter.doctor }}</h3>
					<p>Doctors</p>
				</MyBox>
			</div>
			<!-- ./col -->
			<div class="col-lg-3 col-6">
				<!-- small box -->
				<MyBox
					:color="`blue`"
					:icon="`user-friends`"
					:url="{ name: 'team.index' }"
					:isLoading="loading.team"
				>
					<h3>{{ counter.team }}</h3>
					<p>Teams</p>
				</MyBox>
			</div>
			<!-- ./col -->
		</div>
		<!-- End - Small Boxes -->

	</div>
</template>

<script>
export default {
	data() {
		return {
			counter: {
				company: 0,
				employee: 0,
				doctor: 0,
				team: 0,
				territory: 0,
			},
			loading: {
				company: false,
				employee: false,
				doctor: false,
				team: false,
				territory: false,
				geolocation: false,
			},
		};
	},

	mounted() {
		// this.$store.dispatch("roles/fetchUserRoles");

		this.$nextTick(() => {
			// this.getCounter("company");
			this.getCounter("employee");
			this.getCounter("doctor");
			this.getCounter("team");

			// Fetch & store to Vuex
			// this.fetchSpecialties();
			// this.fetchClassifications();
			// const test = this.$permission("user", "hapus");
			// console.log("HASIL PERMISSION", test);
		});
	},

	methods: {
		getCounter(type) {
			this.loading[type] = true;
			this.$axios
				.get(`dashboard/counter/${type}`)
				.then((resp) => {
					if (resp.data.success) {
						this.counter[type] = resp.data.data;
						this.loading[type] = false;
					}
				})
				.catch((error) => {
					this.$toast.global.server_error();
					this.loading[type] = false;
				});
		},


		/**
		 * Fetch doctor's specialties
		 */
		fetchSpecialties(){
			this.$axios.get(`search-specialty`).then((resp)=> {
				if (resp.data.items && resp.data.items.length) {
					this.$store.dispatch('specialties/storeSpecialties', resp.data.items);
				}
			})
		},

		/**
		 * Fetch doctor's classification
		 */
		fetchClassifications(){
			this.$axios.get(`search-classification`).then((resp)=> {
				if (resp.data.items && resp.data.items.length) {
					this.$store.dispatch('classifications/storeClassifications', resp.data.items);
				}
			})
		}
	},

	
};
</script>

<style>
</style>