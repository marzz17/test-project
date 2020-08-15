
<template>
  <div>
    <div class="row">
      <div class="col-md-12">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Campaign</span>
          </div>
          <campaign />
        </el-card>
      </div>
    </div>
    <div class="row aligntop">
      <div class="col-md-12">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Templates</span>
          </div>
          <emailtemplates />
        </el-card>
      </div>
    </div>
    <div class="row aligntop">
      <div class="col-md-8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>New Message</span>
          </div>
          <div class="row">
            <div class="col-md-12">
              <el-select
                v-model="template"
                filterable
                placeholder="Select Template"
                no-data-text="No Data"
                no-match-text="No data found!"
                style="min-width:100%"
                @change="get_templateandcontacts"
              >
                <el-option
                  v-for="item in templates"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                ></el-option>
              </el-select>
              <el-input
                type="text"
                class="aligntop"
                placeholder="Subject"
                v-model="subject"
                clearable
                size="mini"
              />
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

              <el-popconfirm
                confirmButtonText="Send"
                cancelButtonText="Cancel"
                icon="el-icon-info"
                iconColor="blue"
                title="Are you sure to send this email?"
                @onConfirm="getsmstext"
              >
                <el-button
                  slot="reference"
                  class="aligntop"
                  size="small"
                  type="primary"
                  :loading="sendemailloading"
                >Send Email</el-button>
              </el-popconfirm>
            </div>
          </div>
        </el-card>
      </div>
      <div class="col-md-4">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Variable Template Used Information</span>
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
      sendemailloading: false,
      template: "",
      templates: [],
      templateused: [],
      templateidused: ""
    };
  },
  created() {
    this.gettemplates();
  },
  methods: {
    createdeliveries() {
      axios
        .post("/createtdelivery", { data: this.newstringemail })
        .then(res => {
          if ((res.status = 201)) {
            this.$notify({
              title: "Success!",
              message: `Successfully Save Delivery!`,
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
    gettemplates() {
      axios
        .get("/gettemplates")
        .then(res => {
          this.templates = res.data.templates;
        })
        .catch(error => {
          console.log(error);
        });
    },
    get_templateandcontacts() {
      axios
        .get("/gettemplatesbyid/" + this.template)
        .then(res => {
          this.templateused = res.data.templates;
          this.templateidused = this.templateused[0]["contact_id"];
          this.subject = this.templateused[0]["subject"];
          this.messages = this.templateused[0]["message"];
          this.csvdetails = res.data.contacts;
          let g = res.data.contacts;
          let keys = Object.keys(g[0]);
          this.csvheader = keys;
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
        this.createdeliveries();
      }, 1000);
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
          status: "Not Sent",
          template: this.template,
          contact: this.templateidused
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