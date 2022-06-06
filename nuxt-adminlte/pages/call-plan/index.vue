<template>
	<div>
		<portal to="header-title">
			<span>Call Plan</span>
		</portal>
		<div class="row">
			<div class="col-6">
				<vc-date-picker
					v-model="selectedDate"
					:model-config="modelConfig"
					:attributes="attributes"
					@update:to-page="updatePage"
					:disabled-dates="{weekdays: [1]}"
				/>
			</div>
			<div class="col-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Activities at <span class="font-weight-bold">{{ $moment(selectedDate).format('ll') }}</span></h3>
						<div class="card-tools left">
							<nuxt-link @click.native="()=> {loading = true}" class="btn btn-default" :class="{'disabled': loading}" v-if="$permission('call-plan', 'create')" :to="{name: 'call-plan.create', params: { date: selectedDate }}">
								<span class="text-body">
									<span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
									<i v-if="!loading" class="fa fa-plus"></i> 
									{{(!loading) ? 'Add Activity' : 'Loading...'}}
								</span>
							</nuxt-link>
						</div>
					</div>
					<!-- <div class="card-body">
						<h3>Selected Date at {{ $moment(selectedDate).format('ll') }}</h3>						
					</div> -->
					<table class="table table-hover">
						<thead>
							<th>No.</th>
							<th>Activity</th>
							<th>Created At</th>
							<th>Actions</th>
						</thead>
						<tbody>
							<tr v-for="(item, key) in activities" :key="item.id">
								<td>{{ ++key }}</td>
								<td>{{item.activity.name}}</td>
								<td>{{$moment(item.created_at).format('lll')}}</td>
								<td>
									<nuxt-link @click.native="()=> {loading = true}" class="btn btn-sm btn-primary" :class="{'disabled': loading}" :to="{name: 'call-plan.edit', params: {id: item.id, date: selectedDate }}">
										Sechedule Detail
									</nuxt-link>
									<button class="btn btn-sm btn-danger" :class="{'disabled': loading}" v-if="$permission('call-plan', 'view')" @click.prevent="deleteActivity(item.id)">Delete</button>
								</td>
							</tr>
							<tr v-if="!activities.length">
								<td colspan="4">
									<span><i>No activities yet, click Add to new one!</i></span>
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
import CallPlanCalendar from "~/components/CallPlanCalendar";
export default {
	components: {
		CallPlanCalendar,
	},

	data() {
		return {
			loading: false,
			selectedDate: this.$moment().format("YYYY-MM-DD"),
			activities: [],
			modelConfig: {
				type: "string",
				mask: "YYYY-MM-DD", // Uses 'iso' if missing
			},
		};
	},

	async asyncData({ params, $axios, $moment }) {
		const attributes = await $axios
			.get(`call-plan/monthly-schedule`, {
				params: {
					year: $moment().format('YYYY'),
					month: $moment().format('MM')
				}
			})
			.then((resp) => {
				if (resp.data.data && resp.data.data.length) {
					return resp.data.data.map(function (item) {
						return {
							highlight: {
								color: "red",
								fillMode: "outline",
							},
							dates: $moment(
								item.schedule_date_month,
								"YYYY-MM-DD"
							).format(),
						};
					});
				}
				return [];
			});

		return {
			attributes,
		};
	},

	computed: {},

	methods: {
		async getScheduleActivities(){

			if (!this.selectedDate) {
				this.$toast.info(`Please, select a date on each number!`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 3000,
						position: 'bottom-center',
					});
				return;
			}

			this.activities = await this.$axios.get(`call-plan/schedule-activities`, {
				params: {
					selected_date: this.selectedDate
				}
			}).then(function (resp) {
				return resp.data.data
			});
		},

		async updatePage(page){
			this.activities = [];

			const attributes = await this.$axios
				.get(`call-plan/monthly-schedule`, {
					params: {
						year: page.year, // $moment().format('YYYY'),
						month: page.month, // $moment().format('MM')
					}
				})
				.then((resp) => {
					if (resp.data.data && resp.data.data.length) {
						return resp.data.data.map(function (item) {
							return {
								highlight: {
									color: "red",
									fillMode: "outline",
								},
								dates: item.schedule_date_month,
							};
						});
					}
					return [];
				});

			this.attributes = attributes;
		},

		deleteActivity(id){

			this.$swal.fire({
				title: 'Sure to delete activity?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`call-plan/schedule-activities/${id}`);
						
						this.activities = await this.$axios.get(`call-plan/schedule-activities`, {
							params: {
								selected_date: this.selectedDate
							}
						}).then(function (resp) {
							return resp.data.data
						});
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Activity are not deleted', '', 'info');
				}
			})

			
		},
	},

	watch: {
		'selectedDate': function (newVal, oldVal) {
			this.$nextTick(()=> this.getScheduleActivities());
		}
	},

	mounted(){
		this.$nextTick(()=> this.getScheduleActivities());
	}
};
</script>

<style lang="postcss" scoped>
.custom-calendar {
	border-radius: 0;
}
/deep/ .vc-container {
	width: inherit;
}
/deep/ .vc-header {
	background-color: #b9def8;
	padding: 10px 0;
}

/deep/ .vc-weeks {
	padding: 0;
}

/deep/ .vc-weekday {
	background-color: #dce9f7;
	border-bottom: 1px solid #eaeaea;
	border-top: 1px solid #eaeaea;
	color: rgb(0, 0, 0);
	padding: 5px 0;
}

/deep/ .vc-day-dots {
	margin-bottom: 5px;
}

/deep/ .vc-day {
	border: 1px solid #b8c2cc;
	padding: 0 5px 3px 5px;
	text-align: left;
	height: 90px;
	min-width: 90px;
	background-color: white;
	&:not(.on-bottom) {
		border-bottom: 1px solid #b8c2cc;
		&.weekday-1 {
			border-bottom: 1px solid #b8c2cc;
		}
	}
	&:not(.on-right) {
		border-right: 1px solid #b8c2cc;
	}
}

/deep/ .vc-day:hover {
	position: relative;
	height: 100%;
	background-color: rgba(0, 255, 255, 0.171);
}

/deep/ .weekday-1 {
	background-color: #eff8ff;
}

/deep/ .is-today {
	background-color: #b9f36c;
}

::-webkit-scrollbar {
	width: 0px;
}
::-webkit-scrollbar-track {
	display: none;
}
</style>

