
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
              <el-button @click="create_contacts" class="aligntop" size="small" type="primary">Try</el-button>
            </div>
          </div>
        </el-card>
      </div>
      <div class="col-md-4">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <input
              type="file"
              ref="file"
              name="File Upload"
              v-on:change="onFileChange"
              accept=".csv"
              style="float: left; width:200px"
            />
            <span style="float: right;margin-top:4px;">Variable Information</span>
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
      <div class="col-md-8">
        <el-card class="box-card">
          <el-table
            :data="newstringemail.filter(data => !search || data.subject.toLowerCase().includes(search.toLowerCase()))"
            style="width: 100%"
          >
            <el-table-column label="Recipient" prop="recipient"></el-table-column>
            <el-table-column label="Subject" prop="subject"></el-table-column>
            <el-table-column label="Message" prop="message"></el-table-column>
            <el-table-column label="Status" prop="status">
              <template slot-scope="scope">
                <el-tag
                  :type="scope.row.status === 'Sent' ? 'success' : 'warning'"
                  disable-transitions
                >{{scope.row.status}}</el-tag>
              </template>
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
    create_contacts() {
      axios
        // .post("/createcontacttable")
        .post("/createcontacts", { data: this.csvdetails })
        .then(res => {
          console.log(res.data);
        })
        .catch(error => {
          console.log(error);
        });
    },
    sendEmail() {
      for (let i = 0; i < this.newstringemail.length; i++) {
        let vm = this;
        let data = vm.newstringemail[i];
        Email.send({
          Host: "smtp.gmail.com",
          Username: "brassrabbit2020@gmail.com",
          Password: "A123456789.0",
          To: `${data["recipient"]}`,
          From: "brassrabbit2020@gmail.com",
          Subject: `${data["subject"]}`,
          Body: `${data["message"]}`
        }).then(function(message) {
          if (message == "OK") {
            vm.newstringemail[i]["status"] = "Sent";
            vm.$notify({
              title: "Message Sent!",
              message: `Successfully Sent! to ${data["recipient"]}`,
              type: "success"
            });
          } else {
            vm.newstringemail[i]["status"] = "UnSent";
            vm.$notify({
              title: "Error!",
              message: "Something went wrong!!",
              message,
              type: "error"
            });
          }
        });
      }
      setTimeout(() => {
        this.sendemailloading = false;
      }, 10000);
    },
    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.file = files[0];
      this.getexcelfiledetails();
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
          message: message,
          status: "Not Sent"
        };
        this.newstringemail.push(valudetails);
      }
      if (this.newstringemail.length > 0) {
        this.sendEmail();
      } else {
        vm.$notify({
          title: "No data to send!",
          message: "Something went wrong!!",
          message,
          type: "error"
        });
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