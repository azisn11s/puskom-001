<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Employee</span>
			<span v-if="subTitle"> - {{ subTitle.name }}</span>
			<nuxt-link v-if="$permission('employee', 'create')" tag="a" to="/employees/create" class="btn btn-primary" style="margin-left:1%;">
				<span><i class="fas fa-user-tie"></i> Add</span>
			</nuxt-link>
			<button-import-employee @importSuccess="onImportSuccess" />
		</portal>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<h3 class="card-title">List</h3>
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
								:layout="['total', 'prev', 'pager', 'next']"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style scoped>
.table-pagination {
	margin-top: 20px;
	text-align: right;
}
</style>

<script>
import ButtonImportEmployee from "~/components/ButtonImportEmployee.vue";

export default {

	components: {
		ButtonImportEmployee
	},

	async asyncData({route, $axios}){
		let subTitle
		
		if (route.query.company) {
			subTitle = await $axios.get(`companies/${route.query.company}`).then(resp=> {
				if (resp.data.success) {
					return resp.data.data
				}

				return null;
			})
		}

		return {
			subTitle
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
					field: "first_name", key: "b", title: "Name", align: "left", sortBy: "", 
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = {
							name: `${row[column.field]} ${row['last_name']}`,
							code: `${row['employee_code']}`,
							gender: row['gender'] === 'M' ? 'fas fa-mars text-blue' : 'fas fa-venus text-pink'
						};
						return (<div>
							<span><b>{ text.name }</b>  <i class={text.gender}></i></span>
							<div class="text-muted">{ text.code }</div>
						</div>);
					},
				},
				{ 
					field: "employee_type_id", key: "g", title: "Employee Type", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row.employee_type) {
							return '-';
						}

						const text = {
							name: row.employee_type.name,
							code: row.employee_type.code 
						};
						return (<div>
							<div>{text.name} <span class="text-muted">({text.code})</span></div>
						</div>);
					},
				},
				{ 
					field: "email", key: "c", title: "Contacts", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = {
							email: row[column.field] || ' -',
							mobile_number: row['mobile_number'] || ' -'
						};
						return (<div>
							<span><i class="fas fa-envelope-open"></i> { text.email }</span>
							<div><i class="fas fa-phone"></i> { text.mobile_number }</div>
						</div>);
					},
				},
				{ 
					field: "birthdate", key: "d", title: "Age", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = {
							age: this.$moment().diff(row[column.field], 'years'),
							birthdate: this.$moment(row[column.field]).format('ll')
						};
						return (<div>
							<div>{text.age}</div>
							<div class="text-muted">{text.birthdate}</div>
						</div>);
					},
				},
				{ 
					field: "join_date", key: "e", title: "Join Date", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						const text = {
							join_date: this.$moment(row[column.field]).format("ll"),
							company: row.company.name 
						};
						return (<div>
							<div>{text.join_date}</div>
							<div class="text-muted">{text.company}</div>
						</div>);;
					},
				},
				// {
				// 	field: "created_at",
				// 	key: "e",
				// 	title: "Created At",
				// 	align: "center",
				// 	renderBodyCell: ({row, column, rowIndex}, h)=> {
				// 		const text = this.$moment(row[column.field]).format("lll")
				// 		return text;
				// 	},
				// 	sortBy: "desc"
				// },
				{
					field: "",
					key: "f",
					title: "Action",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return (
							<div>
							<div class="btn-group btn-block">
								<button type="button" class="btn btn-warning btn-sm" on-click={()=> this.$router.push(`employees/${row['id']}/edit`)}>
									<i class="fas fa-edit"></i>
								</button>
								<button type="button" class="btn btn-danger btn-sm" on-click={()=> this.deleteRow(row['id'])}> 
									<i class="fas fa-trash"></i>
								</button>
							</div>
							<div class="mt-2" v-show={!row['user']}>
								<button type="button" class="btn btn-block btn-info btn-sm"
									on-click={()=> this.createUserFromEmployee(row['id'])}
								>
									<i class="fas fa-user"></i>&nbsp;
									Create User
								</button>
							</div>
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
			sortParams: {"updated_at": "desc"},
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
		};
	},

	computed: {
		params(){
			return this.$route.query;
		}
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
				const { data } = await this.$axios.get(`employees`, {
					params: {
						page: this.pageIndex,
						per_page: this.pageSize,
						search: this.searchFilter || null,
						sort: this.sortParams,
						...this.params
					},
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
				title: 'Sure to delete employee?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`employees/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Employee is not deleted', '', 'info');
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

		createUserFromEmployee(id){
			this.$swal.fire({
				title: 'Are you sure to create user from this employee record?',
				text: 'User can login into application using defaul password 12345678.',
				showCancelButton: true,
				confirmButtonText: `Ok, Register!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.post(`admin/user-from-employee/${id}`, {
							entity_id: id
						});
						
						this.$swal.fire('User registered successfully!', 'User can login into application using defaul password 12345678.', 'success');
						
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						if (error.response && error.response.status == 403) {
							let message = "Action failed.";

							if (error.response.data) {
								message = error.response.data.message;
							}

							this.$swal.fire('Oops, keep smile!', `${message}`, 'warning');

						} else {
							this.$swal.fire('Register failed!', 'Something went wrong!', 'danger');
						}
						
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('User are not register', '', 'info');
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
};
</script>