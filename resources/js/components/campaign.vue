<template>
  <div>
    <el-tag
      :key="index"
      v-for="(item, index) in campaigns"
      closable
      :disable-transitions="false"
      @close="handleClose(item.id)"
    >{{item.name}}</el-tag>
    <el-input
      class="input-new-tag"
      v-if="inputVisible"
      v-model="inputValue"
      ref="saveTagInput"
      size="mini"
      @keyup.enter.native="handleInputConfirm"
      @blur="handleInputConfirm"
    ></el-input>
    <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Campaigns</el-button>
  </div>
</template>
<script>
export default {
  name: "campaigns",
  data() {
    return {
      campaigns: [],
      inputVisible: false,
      inputValue: ""
    };
  },
  created() {
    this.getcampaigns();
  },
  methods: {
    getcampaigns() {
      axios
        .get("/getcampaigns")
        .then(res => {
          this.campaigns = res.data.campaigns;
          console.log(this.campaigns);
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleClose(index) {
      axios
        .delete("/destroycampaigns/" + index)
        .then(res => {
          if ((res.status = 200)) {
            this.campaigns.splice(index, 1);
            this.$notify({
              title: "Success!",
              message: `Successfully Deleted!`,
              type: "success"
            });
          } else {
            this.$notify({
              title: "Error!",
              message: "Something went wrong!!",
              type: "error"
            });
          }
        })
        .catch(error => {
          console.log(error);
        });
    },

    showInput() {
      this.inputVisible = true;
      this.$nextTick(_ => {
        this.$refs.saveTagInput.$refs.input.focus();
      });
    },

    handleInputConfirm() {
      let inputValue = this.inputValue;
      if (inputValue) {
        axios
          .post("/createcampaigns", { name: this.inputValue })
          .then(res => {
            if ((res.status = 201)) {
              this.getcampaigns();
              this.$notify({
                title: "Success!",
                message: `Successfully Save!`,
                type: "success"
              });
            } else {
              this.$notify({
                title: "Error!",
                message: "Something went wrong!!",
                type: "error"
              });
            }
          })
          .catch(error => {
            console.log(error);
          });
      }
      this.inputVisible = false;
      this.inputValue = "";
    }
  }
};
</script>
<style>
.el-tag + .el-tag {
  margin-left: 10px;
}
.button-new-tag {
  margin-left: 10px;
  height: 32px;
  line-height: 30px;
  padding-top: 0;
  padding-bottom: 0;
}
.input-new-tag {
  width: 90px;
  margin-left: 10px;
  vertical-align: bottom;
}
</style>