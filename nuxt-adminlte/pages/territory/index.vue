<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Territory</span>
			<nuxt-link tag="a" to="/territories/create" class="btn btn-primary" style="margin-left:1%;">
				<span><i class="fas fa-globe"></i> Add</span>
			</nuxt-link>
			<button-export-territory :table-filter="listParams" />
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
								:layout="['total', 'prev', 'pager', 'next', 'sizer']"
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
import ButtonExportTerritory from "~/components/ButtonExportTerritory.vue";

export default {
	components: {
		ButtonExportTerritory
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
					field: "name", key: "b", title: "Territory", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						// const text = {
						// 	territory: `${row[column.field]}`,
						// 	alias: `${row['region']['country'].country_name} (${row['region']['country'].code})`
						// }
						return (<div>
							<div><strong>{`${row[column.field]}`}</strong></div>
							<div class="text-muted">{`${row['alias'] ?? '-'}`}</div>
						</div>);
					}, 
				},
				{ 
					key: "j", title: "PIC", align: "left", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row['employee']) {
							return '-';
						}
						
						return (<div class="">
							<div><strong>{`${row['employee'].first_name} ${row['employee'].last_name}`}</strong></div>
							<div class="text-muted">{`${row['employee'].employee_code}`}</div>
						</div>);
					}, 
				},
				{
					field: "area_id",
					key: "g",
					title: "Area",
					align: "left",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return `${row['area'].area_name}`;
					},
					sortBy: "desc"
				},
				{ 
					field: "team_id", key: "c", title: "Team", align: "left", sortBy: "", 
					renderBodyCell: ({row, column, rowIndex}, h)=> {

						if (!row['team']) {
							return '-';
						}
						const text = {
							team: `${row['team'].name}`,
						}
						return (<div>
							<div><strong>{`${text.team}`}</strong></div>
						</div>);
					}, 
				},
				{ 
					key: "i", title: "Doctors", align: "center", sortBy: "",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row['counter_doctors']) {
							return '-';
						}
						
						return (
							<button type="button" class="btn btn-app" 
								on-click:prevent={()=> this.$router.push({name: `doctor.index`, query: {territory: row['id']}})}
							>
								<i class="fas fa-user-md"></i>
								<b>See All ({ row['counter_doctors'] })</b>
							</button>
						);
					}, 
				},
				
				
				{
					field: "region_id",
					key: "h",
					title: "Region",
					align: "left",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return `${row['area']['region'].region_name}`;
					}
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
					field: "status",
					key: "e",
					title: "Status",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						if (!row[column.field]) {
							return (<i class="text-red">Inactive</i>);
						}

						return (<i class="text-green">Active</i>);
					},
					sortBy: ""
				},
				{
					field: "",
					key: "f",
					title: "Action",
					align: "center",
					renderBodyCell: ({row, column, rowIndex}, h)=> {
						return (
							<div class="btn-group btn-block">
								<button type="button" class="btn btn-warning btn-sm" on-click={()=> this.$router.push(`territories/${row['id']}/edit`)}>
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
			cutomFilter: {}
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
				const { data } = await this.$axios.get(`territories`, {
					params: {
						page: this.pageIndex,
						per_page: this.pageSize,
						search: this.searchFilter,
						sort: this.sortParams
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
				title: 'Sure to delete territory?',
				showCancelButton: true,
				confirmButtonText: `Ok, Delete!`,
			}).then(async (result) => {

				if (result && !result.dismiss) {
					this.$nuxt.$loading.start();
					try {

						await this.$axios.delete(`territories/${id}`);
						
						this.$swal.fire('Deleted successfully!', '', 'success');
						this.getDataFromApi();
						this.$nuxt.$loading.finish();

					} catch (error) {

						this.$swal.fire('Delete failed!', '', 'danger');
						this.$nuxt.$loading.finish();

					}
				} 
				
				if (result && result.dismiss) {
					this.$swal.fire('Territory are not deleted', '', 'info');
				}
			})

			
		},


		/**
		 * On demand methods
		 * 
		 */
		
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

	computed: {
		listParams(){
			return {
				page: this.pageIndex,
				per_page: this.pageSize,
				search: this.searchFilter || null,
				sort: this.sortParams,
				...this.cutomFilter
			}
		}
	},
};
</script>