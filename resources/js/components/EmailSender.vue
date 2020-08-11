
<template>
  <div>
    <div class="row">
      <div class="col-md-8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>New Message</span>
          </div>
          <div class="row">
            <div class="col-md-12">
              <input type="file" name="File Upload" v-on:change="onFileChange" accept=".csv" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <el-input
                class="aligntop"
                type="text"
                placeholder="Subject"
                v-model="subject"
                clearable
                size="medium"
              />
              <el-input
                class="aligntop"
                type="textarea"
                :rows="13"
                maxlength="1500"
                show-word-limit
                placeholder="Compose Message"
                v-model="textarea"
                size="medium"
              />

              <el-button class="aligntop" size="medium" type="primary">Send Email</el-button>
            </div>
          </div>
        </el-card>
      </div>
      <div class="col-md-4">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Variable Information</span>
          </div>
          <div v-for="(item, key,index) in csvheader" :key="index">
            <button
              type="button"
              class="btn btn-primary headervariable btn-sm"
              draggable="true"
              size="sm"
              :id="index"
            >{{ '{ ' + item + ' }'}}</button>
          </div>
        </el-card>
      </div>
    </div>
    <div class="row aligntop">
      <div class="col-md-12">
        <el-card class="box-card">
          <el-table
            :data="newstringemail.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
            style="width: 100%"
          >
            <el-table-column label="Subject" prop="date"></el-table-column>
            <el-table-column label="Message" prop="name"></el-table-column>
            <el-table-column align="right">
              <!-- <template slot="header" slot-scope="scope">
                <el-input v-model="search" size="mini" placeholder="Type to search" />
              </template>-->
            </el-table-column>
          </el-table>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "emailsender",
  data() {
    return {
      subject: "",
      textarea: "",
      file: null,
      csvdetails: [],
      csvheader: [],
      newstringemail: [],
      search: ""
    };
  },
  methods: {
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.file = files[0];
      this.getexcelfiledetails();
    },
    newtext() {
      this.newstring = [];
      let s = this.smstext;
      let r = /\{.*?\}/g;
      for (let i = 0; i < this.csvdetails.length; i++) {
        const add = this.csvdetails[i];
        let newstring = s.replace(r, function(match) {
          return add[match.split(" ")[1]];
        });

        this.newstringemail.push(newstring);
      }
    },
    getsmstext() {
      this.newtext();
    },
    getexcelfileheader() {
      let vm = this;
      this.$papa.parse(vm.file, {
        header: false,
        skipEmptyLines: true,
        truncated: false,
        complete: function(results, file) {
          vm.csvheader = results.data[0];
        }
      });
    },
    getexcelfiledetails() {
      let vm = this;
      this.$papa.parse(this.file, {
        header: true,
        escapeChar: '"',
        newline: "\r\n",
        delimiter: ",",
        linebreak: "↵",
        escapeFormulae: false,
        aborted: false,
        truncated: false,
        skipEmptyLines: true,
        transformHeader: true,
        complete: function(results, file) {
          vm.csvdetails = results.data;
        }
      });
      vm.getexcelfileheader();
    }
  }
};
document.addEventListener("dragstart", function(event) {
  event.dataTransfer.setData("Text", event.target.innerHTML);
});
</script>
<style >
.aligntop {
  margin-top: 10px;
}
.headervariable {
  margin-top: 2px;
}
.el-card__header {
  padding: 7px 20px !important;
  /* background: #d54b3d !important;
  color: #fff !important; */
}
</style>