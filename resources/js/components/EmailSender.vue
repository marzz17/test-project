
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
              <el-input type="text" placeholder="Subject" v-model="subject" clearable size="mini" />
              <el-input
                class="aligntop"
                type="textarea"
                :rows="13"
                maxlength="1500"
                show-word-limit
                placeholder="Compose Message"
                v-model="messages"
                size="mini"
              />

              <el-button
                @click="getsmstext"
                class="aligntop"
                size="small"
                type="primary"
                :loading="sendemailloading"
              >Send Email</el-button>
            </div>
          </div>
        </el-card>
      </div>
      <div class="col-md-4">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Variable Information</span>
            <input
              type="file"
              ref="file"
              name="File Upload"
              v-on:change="onFileChange"
              accept=".csv"
              style="float: right; width:200px"
            />
          </div>
          <button
            v-for="(item, key,index) in csvheader"
            :key="index"
            type="button"
            class="btn btn-primary headervariable btn-sm"
            style="margin:0 5px 5px 0;"
            draggable="true"
            size="sm"
            :id="index"
          >{{ '{ ' + item + ' }'}}</button>
        </el-card>
      </div>
    </div>
    <div class="row aligntop">
      <div class="col-md-12">
        <el-card class="box-card">
          <el-table
            :data="newstringemail.filter(data => !search || data.subject.toLowerCase().includes(search.toLowerCase()))"
            style="width: 100%"
          >
            <el-table-column label="Recipient" prop="recipient"></el-table-column>
            <el-table-column label="Subject" prop="subject"></el-table-column>
            <el-table-column label="Message" prop="message"></el-table-column>
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
      messages: "",
      file: null,
      csvdetails: [],
      csvheader: [],
      newstringemail: [],
      search: "",
      sendemailloading: false
    };
  },
  methods: {
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.file = files[0];
      this.getexcelfiledetails();
      console.log(files[0]);
    },
    newtext() {
      this.newstringemail = [];
      let s = this.subject;
      let m = this.messages;
      let r = /\{.*?\}/g;
      for (let i = 0; i < this.csvdetails.length; i++) {
        const add = this.csvdetails[i];
        //getsubject
        let subject = s.replace(r, function(match) {
          return add[match.split(" ")[1]];
        });
        //getmessage
        let message = m.replace(r, function(match) {
          return add[match.split(" ")[1]];
        });

        let valudetails = {
          recipient: this.csvdetails[i]["email"],
          subject: subject,
          message: message
        };
        this.newstringemail.push(valudetails);
      }
    },
    getsmstext() {
      if (this.file == null) {
        this.$notify({
          title: "No CSV file selected!",
          message: "Please select csv file first to proceed!",
          type: "warning"
        });
        this.$nextTick(() => this.$refs.file.focus());
      }
      this.sendemailloading = true;
      try {
        this.newtext();
        this.sendemailloading = false;
      } catch (error) {
        this.sendemailloading = false;
      }
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