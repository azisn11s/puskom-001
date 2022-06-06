<template>
  <div class="container-btn-export">
      <button
            class="btn btn-outline-primary"
            title="Export shown list of doctors into Excel file."
            @click="downloadExcel"
        >
            <span><i class="fas fa-download"></i> Export (Excel)</span>
        </button>
  </div>
</template>

<script>
export default {
    props: {
        tableFilter: {
			type: Object,
			default: null
		}
    },

    data(){
        return {
            isLoading: false,
        }
    },

    methods: {
        async downloadExcel(){
            this.$nuxt.$loading.start();
            this.isLoading = true;

            try {

                let downloadUrl = '/exports/excel/doctors';

				let result = await this.$axios({
					url: `${downloadUrl}`, // process.env.apiUrl + 
					params: this.tableFilter || null,
					method: "GET",
					responseType: "blob",
				}).then((response) => {
					const blob = new Blob([response.data], {
						type: response.data.type,
					});

					// IE doesn't allow using a blob object directly as link href
					// instead it is necessary to use msSaveOrOpenBlob
					if (window.navigator && window.navigator.msSaveOrOpenBlob) {
						window.navigator.msSaveOrOpenBlob(blob);
						return;
					}

					const data = window.URL.createObjectURL(blob);
					const link = document.createElement("a");
					link.href = data;
					link.download = `doctors.xlsx`; // `${this.item.file.reformat_filename}`;
					link.click();

					setTimeout(function () {
						// For Firefox it is necessary to delay revoking the ObjectURL
						window.URL.revokeObjectURL(data);
					}, 100);
					this.isLoading = false;
					this.$nuxt.$loading.finish();
				});
                
            } catch (err) {
                if (err.response && err.response.data.code == 403) {
					this.$swal.fire(
						`Gagal, ${err.response.data.error}`,
						"",
						"error"
					);
				} else {
					this.$swal.fire(
						"Terjadi kesalahan",
						"Silakan hubungi Admin",
						"error"
					);
				}
				this.isLoading = false;
				this.$nuxt.$loading.finish();
            }
        }
    }
}
</script>

<style scoped>
.container-btn-export{
    display: initial;
}
</style>