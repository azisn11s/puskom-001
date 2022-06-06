<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Doctor</span>
			<nuxt-link tag="a" to="/doctors/create" class="btn btn-primary" style="margin-left:1%;">
				<span><i class="fas fa-user-md"></i> Add</span>
			</nuxt-link>
			<button-import-doctor @importSuccess="onImportSuccess" />
			<button-export-doctor :table-filter="listParams" />
		</portal>

		<!-- Custom Filter -->
		<div class="card">
			<div class="card-body">
                <div class="row">
					<label class="col-1 col-form-label">Filter By:</label>
                  <div class="col-3">
                    <dropdown-specialties @onSelectedItem="onSelectedSpecialty" />
                  </div>
                  <div class="col-3">
                    <dropdown-classifications @onSelectedItem="onSelectedClassification" />
                  </div>
				  <div class="col-3">
					  <dropdown-territories @onSelectedItem="onSelectedTerritories" />
				  </div>
                </div>
              </div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">
							List
						</h3>
						<div class="card-tools">
							<div class="input-group input-group-sm" style="max-width: 200px;">
								<input type="text" v-model="searchFilter" @keyup="()=> { this._.delay(()=> this.getDataFromApi(), 1000) }" class="form-control float-right" placeholder="Search">

								<div class="input-group-append">
								<button type="submit" class="btn btn-default" @click="()=> { this.getDataFromApi();}">
									<i class="fas fa-search"></i>
								</button>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<vue-element-loading :active="isLoading" :is-full-screen="false" style="z-index: 1"/>
						<ve-table :columns="columns" :table-data="tableData" id="table-container" :sort-option="sortOption" />
						<div class="table-pagination">
                            <!-- 'sizer', 'jumper' -->
							<ve-pagination
								:total="totalCount"
								:page-index="pageIndex"
								:page-size="pageSize"
								@on-page-number-change="pageNumberChange"
								@on-page-size-change="pageSizeChange"
								:layout="['total', 'prev', 'pager', 'next', 'sizer']"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal History Approval -->
		<modal
			:id="`modal-history-approval`"
			:title="`History of Approval - Doctor`"
			:enableSubmitBtn="false"
			:loading="isLoadingHistoryApproval"
		>
			<template :slot="'body'">
				<div class="row" v-if="isApprovalEnabled">
					<div class="col-lg-12">
						<div class="card bg-gradient-warning">
							<div class="card-header">
								<i class="fa fa-info"></i>
								<strong>Approval Needed</strong>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-lg-2">
										<dl>
											<dt>Action</dt>
											<dd>{{ selectedDoctor.last_log_record.record_action.label }}</dd>

											
										</dl>
									</div>
									<div class="col-lg-5">
										<dl>
											<dt>User</dt>
											<dd>{{ selectedDoctor.last_log_record.created_by_user.email }}</dd>

										</dl>
									</div>
									<div class="col-lg-5">

											<dt>Requested At</dt>
											<dd>{{ $moment(selectedDoctor.last_log_record.created_at).format('lll') }}</dd>
									</div>
								</div>
								<!-- {{ selectedDoctor.last_log_record }} -->
							</div>
							<div class="card-footer d-flex justify-content-center">
								<button class="btn btn-danger btn-lg btn-flat" 
									:disabled="!$permission('doctor', 'approval')"
									@click="rejectRecordChanges(selectedDoctor.id)"
								>
									<i class="fas fa-redo"></i>
									Reject
								</button>
								<button class="btn btn-primary btn-lg btn-flat"
									:disabled="!$permission('doctor', 'approval')"
									@click="approveRecordChanges(selectedDoctor.id)"
								>
									<i class="fas fa-check"></i>
									Approve
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="card">
							<div class="card-header">
								<i class="fa fa-info-circle"></i>
								<strong>Logs Record</strong>
							</div>
							<div class="card-body">
								<ul class="products-list pl-2 pr-2">
									<li v-for="(item, key) in historiesApproval" :key="item.id" class="mb-2">
										<div class="">
											<div class="product-title">
												<span>{{++key}}.</span>
												<div class="badge badge-success">
													<b>{{ item.record_action.slug }}</b>
												</div>
												<div class="badge" 
													:class="{'badge-success': item.record_status == 'approved', 'badge-warning': item.record_status == 'waiting', 'badge-danger': item.record_status == 'rejected'}"
												>
													{{ item.record_status }}
												</div>
												<span class="badge badge-warning float-right">
													<i class="fa fa-clock"></i>
													{{ $moment(item.updated_at).format('lll') }}
												</span>
											</div>
											<div class="product-description">
												<div class="card">
													<div class="card-header">
														<div class="card-title w-100">
															<a class="d-block w-100 collapsed" data-toggle="collapse" :href="`#record-${item.id}`" aria-expanded="false">
																See record detail
															</a>
															<span class="badge badge-default float-right">
																<i class="fa fa-user"></i>
																{{ item.created_by_user.name }} ({{item.created_by_user.email}})
															</span>
															<!-- data-parent="#accordion" -->
															<div :id="`record-${item.id}`" class="collapse"  style="">
																<div class="card-body">
																	<div class="row">
																		<div class="col-lg-12 small">
																			<dl>
																				<dt class="col-sm-4">Name</dt>
																				<dd class="col-sm-8">{{ item.body.first_name }} {{ item.body.last_name }}</dd>

																				<dt class="col-sm-4">Title</dt>
																				<dd class="col-sm-8">{{ item.body.front_title }} | {{ item.body.back_title || '...' }}</dd>

																				<dt class="col-sm-4">Gender</dt>
																				<dd class="col-sm-8">
																					<span v-if="item.body.gender === 'M'">{{'Male'}}</span>
																					<span v-if="item.body.gender === 'F'">{{'Female'}}</span>
																				</dd>

																				<dt class="col-sm-4">Current Hospital</dt>
																				<dd class="col-sm-8">{{ item.body.current_hospital || '-' }}</dd>

																				<dt class="col-sm-4">City</dt>
																				<dd class="col-sm-8">{{ item.body.city || '-' }}</dd>

																				<dt class="col-sm-4">Market</dt>
																				<dd class="col-sm-8">{{ item.body.market || '-' }}</dd>

																				<dt class="col-sm-4">Address</dt>
																				<dd class="col-sm-8">{{ item.body.address || '-' }}</dd>

																				<dt class="col-sm-4">Status Active</dt>
																				<dd class="col-sm-8">
																					<span v-if="item.body.is_active" class="text-success"><b>Active</b></span>
																					<span v-if="!item.body.is_active" class="text-danger"><b>Inactive</b></span>
																				</dd>
																			</dl>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
							
						</div>
					</div>
				</div>
			</template>
		</modal>
	</div>
</template>
<style scoped>
.table-pagination {
	margin-top: 20px;
	text-align: right;
}
</style>

<script>
import ButtonImportDoctor from "~/components/ButtonImportDoctor.vue";
import ButtonExportDoctor from "~/components/ButtonExportDoctor.vue";
import DropdownSpecialties from "~/components/DropdownSpecialties.vue";
import DropdownClassifications from "~/components/DropdownClassifications.vue";
import DropdownTerritories from "~/components/DropdownTerritories.vue";
import Modal from "~/components/Modal";

export default {

	watchQuery: true,

	components: {
		ButtonImportDoctor,
		ButtonExportDoctor,
		DropdownSpecialties,
		DropdownClassifications,
		DropdownTerritories,
		Modal
	},
	
	computed: {
		params(){
			return this.$route.query;
		},
		listParams(){
			return {
				page: this.pageIndex,
				per_page: this.pageSize,
				search: this.searchFilter || null,
				sort: this.sortParams,
				...this.params,
				...this.cutomFilter
			}
		},
		isApprovalEnabled(){
			return this.selectedDoctor && this.selectedDoctor.last_log_record.record_status == 'waiting';
		}
	},
	
	data() {
		return {
            loadingInstance: null,
			isLoading: false,
			columns: [
				{
					field: "",
					key: "a",
					title: "No.",
					width: 50,
					align: "center",
					renderBodyCell: ({ row, column, rowIndex }, h) => {
						// return ++rowIndex;
                        return (this.pageIndex - 1) * this.pageSize + 1 + rowIndex;
					}					
				},
				{ 
					field: "first_name", key: "b", title: "Fullname", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = {
							fullname: row['last_name'] ? `${row[column.field]} ${row['last_name']}` : `${row[column.field]}`,
							gender: row['gender'] === 'M' ? 'fas fa-mars text-blue' : 'fas fa-venus text-pink',
							title: (row.front_title || '...') + ' | ' + (row.back_title || '...')
						}

						return (<div>
							<span><strong>{`${text.fullname}`}</strong> <i class={text.gender}></i></span>
							<div class="text-muted">{`${text.title}`}</div>
						</div>);
						
					}, 
				},
				{ 
					field: "current_hospital", key: "c", title: "Hospital", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row[column.field]) {
							return '-';
						}
						return (<strong>{`${row[column.field]}`}</strong>);
					}, 
				},
				{ 
					field: "specialty", key: "g", title: "Specialty", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row.specialty) {
							return '-';
						}

						return (<div>{`${row.specialty.specialty}`}</div>);
					}, 
				},
				{ 
					field: "classification", key: "h", title: "Classification", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row.classification) {
							return '-';
						}

						return (<div>{`${row.classification.classification}`}</div>);
					}, 
				},
				{
					field: "created_at",
					key: "d",
					title: "Created At",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = this.$moment(row[column.field]).format("lll")
						return text;
					},
					sortBy: "desc"
				},
				
				{
					key: "e",
					title: "Status Approval",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {

						if (row.last_log_record.record_status == 'waiting') {
							return (<button class="btn btn-block btn-sm btn-outline-warning" data-toggle="modal" data-target="#modal-history-approval"
								on-click={()=> this.showHistoryApproval(row['id'])}
							>
								<i class="fa fa-zoom"></i>
								<b>{row.last_log_record.record_status} - {row.last_log_record.record_action.slug}</b>
							</button>);
						}

						if (row.last_log_record.record_status == 'rejected') {
							return (<button class="btn btn-block btn-sm btn-outline-danger" data-toggle="modal" data-target="#modal-history-approval"
								on-click={()=> this.showHistoryApproval(row['id'])}
							>
								<i class="fa fa-search"></i>&nbsp;&nbsp;
								<b>{row.last_log_record.record_status} - {row.last_log_record.record_action.slug}</b>
							</button>);
						}

						if (row.last_log_record.record_status == 'approved') {
							return (<button class="btn btn-block btn-sm btn-outline-success" data-toggle="modal" data-target="#modal-history-approval"
								on-click={()=> this.showHistoryApproval(row['id'])}
							>
								<i class="fa fa-zoom"></i>
								<b>{row.last_log_record.record_status} - {row.last_log_record.record_action.slug}</b>
							</button>);
						}

						return '';

						// OLD
						// if (!row[column.field]) {
						// 	return (<i class="text-red">Inactive</i>);
						// }

						// return (<i class="text-green">Active</i>);
					},
					sortBy: ""
				},
				{
					field: "",
					key: "f",
					title: "Action",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {

						if (row['deleted_at']) {
							return ('-');	
						}

						return (
							<div class="btn-group btn-block">
								<button type="button" class="btn btn-warning btn-sm" on-click={()=> this.$router.push(`/doctors/${row['id']}/edit`)}>
									<i class="fas fa-edit"></i>
								</button>
								<button type="button" class="btn btn-danger btn-sm" on-click={()=> this.deleteRow(row['id'])}> 
									<i class="fas fa-trash"></i>
								</button>
							</div>
							
						);
					}
				},
			],
			// page index
			pageIndex: 1,
			// page size
			pageSize: 10,
			totalCount: 0,
			tableData: [],
            searchFilter: "",
			sortParams: {},
			sortOption: {
				// sort always
				sortAlways: true,
				sortChange: (params) => {
					// console.log("sortChange::", params);
					if (Object.keys(params).length) {
						this.sortParams = params;
						this.$nextTick(()=>{
							this.getDataFromApi();
						});
					}
				},
			},
			cutomFilter: {},


			/**
			 * On-demand data
			 */
			isLoadingHistoryApproval: false,
			historiesApproval: [],
			selectedDoctor: null,
		};
	},
    
	methods: {

		/**
		 * Required methods
		 * 
		 */

		// page number change
		pageNumberChange(pageIndex) {
			this.pageIndex = pageIndex;
            this.getDataFromApi();
		},

		// page size change
		pageSizeChange(pageSize) {
			this.pageIndex = 1;
			this.pageSize = pageSize;
            this.getDataFromApi();
		},

		// Get table data from API
		async getDataFromApi() {
            this.isLoading = true;
			// this.loadingInstance.show();
			try {
				const { data } = await this.$axios.get(`doctors`, {
					params: this.listParams,
				});

				this.totalCount = data.total;
				this.tableData = data.data;
            	this.isLoading = false;

			} catch (error) {
				// console.log("ERROR!!!!!!!!!!!", error);
				this.$toast.global.server_error();
            	this.isLoading = false;
			}
		},

		deleteRow(id){

			this.$swal.fire({
				title: 'Sure to delete a doctor?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`doctors/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Doctor are not deleted', '', 'info');
				}
			})

			
		},


		/**
		 * On demand methods
		 * 
		 */
		onImportSuccess(data){
			if (data.success) {
				this.getDataFromApi();
			}
		},

		onSelectedSpecialty(resp = null){
			if (resp && resp.code) {
				this.cutomFilter = {...this.cutomFilter, specialty: resp.code}
			}

			if (!resp || !resp.code) {
				this.cutomFilter = {...this.cutomFilter, specialty: null}
			}

			this.$nextTick(()=> {
				this.getDataFromApi();
			})
		},

		onSelectedClassification(resp = null){
			if (resp && resp.code) {
				this.cutomFilter = {...this.cutomFilter, classification: resp.code}
			}

			if (!resp || !resp.code) {
				this.cutomFilter = {...this.cutomFilter, classification: null}
			}

			this.$nextTick(()=> {
				this.getDataFromApi();
			})
		},

		onSelectedTerritories(resp = null){
			console.log(resp);
			if (resp && resp.length > 0) {
				this.cutomFilter = {...this.cutomFilter, territories: resp.map(function(item){
					return item.code;
				})}
			}
			if (!resp || resp.length < 1) {
				this.cutomFilter = {...this.cutomFilter, territories: null}
			}

			this.$nextTick(()=> {
				this.getDataFromApi();
			})
		},

		showHistoryApproval(id){
			this.isLoadingHistoryApproval = true
			this.historiesApproval = [];

			// Get doctor detail
			this.$axios.get(`doctors/${id}`).then((resp)=> {
				if (resp.data.success) {
					this.selectedDoctor = resp.data.data
				}
			})
			.catch((error)=> {
				console.log('SOMETHING WENT WRONG WHILE GET DOCTOR', error);

			});
			// Get log histories
			this.$axios.get(`approval/histories/doctor/${id}`).then((resp)=> {
				if (resp.data.success) {
					this.historiesApproval = resp.data.data;
				}
				this.isLoadingHistoryApproval = false
			})
			.catch((error)=> {
				this.isLoadingHistoryApproval = false
				this.$swal.fire('Failed to load histories approval!', '', 'danger');
				console.log('SOMETHING WENT WRONG WHILE GET HISTORIES APPROVAL', error);
			})
		},

		/**
		 * Approval actions (Rejection & Approve)
		 */
		rejectRecordChanges(id){
			this.$swal.fire({
				title: 'Sure to reject latest doctor action?',
				text: 'After rejected, all fields in this doctor will be rollback into before.',
				showCancelButton: true,
				confirmButtonText: `Ok, Reject!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.post(`approval/reject/doctor`, {
							entity_id: id
						});

						await this.$axios.get(`approval/histories/doctor/${id}`).then((resp)=> {
							if (resp.data.success) {
								this.historiesApproval = resp.data.data
							}
						})

						
						this.$swal.fire('Rejected successfully!', 'Record has been rollback to status before.', 'success');
						
						this.selectedDoctor = null;
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						if (error.response && error.response.status == 403) {
							let message = "Action failed.";

							if (error.response.data) {
								message = error.response.data.message;
							}

							this.$swal.fire('Approval Ignored!', `${message}`, 'warning');

						} else {
							this.$swal.fire('Approval failed!', 'Something went wrong!', 'danger');
						}
						
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Doctor are not rejected', '', 'info');
				}
			})

			
		},

		approveRecordChanges(id){
			this.$swal.fire({
				title: 'Sure to approve this doctor action?',
				text: 'After approved, all fields in this doctor will be on the latest changes.',
				showCancelButton: true,
				confirmButtonText: `Ok, Approve!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.post(`approval/approve/doctor`, {
							entity_id: id
						});

						await this.$axios.get(`approval/histories/doctor/${id}`).then((resp)=> {
							if (resp.data.success) {
								this.historiesApproval = resp.data.data
							}
						})

						
						this.$swal.fire('Approved successfully!', 'Record has been saved.', 'success');
						
						this.selectedDoctor = null;
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						if (error.response && error.response.status == 403) {
							let message = "Action failed.";

							if (error.response.data) {
								message = error.response.data.message;
							}

							this.$swal.fire('Approval Ignored!', `${message}`, 'warning');

						} else {
							this.$swal.fire('Approval failed!', 'Something went wrong!', 'danger');
						}

						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Doctor are not approve', '', 'info');
				}
			})

			
		},
	},

	/**
	 * Lifecycle
	 * 
	 */
	created() {
		this.getDataFromApi();
	},
	mounted() {
		// this.loadingInstance = this.$veLoading
		//   veLoading({
		//     target:"#table-container",
		//     name: "wave",
		// 	tip: "Loading..."
		//   });
    },

	watch: {
		"params.specialty": function(newVal, oldVal){
			if (newVal != oldVal) {
				this.getDataFromApi();
			}
		}
	}
};
</script>