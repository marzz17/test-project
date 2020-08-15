
<template>
  <div>
    <div class="row">
      <div class="col-md-8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>New Templates</span>
          </div>
          <div class="row">
            <div class="col-md-12">
              <el-input
                class="aligntop"
                type="text"
                placeholder="Template Name"
                v-model="template.templatename"
                clearable
                size="mini"
              />
              <el-select
                class="aligntop"
                v-model="template.campaign"
                filterable
                placeholder="Select Campaign"
                no-data-text="No Data"
                no-match-text="No data found!"
                style="min-width:100%"
              >
                <el-option
                  v-for="item in campaigns"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                ></el-option>
              </el-select>
              <el-input
                class="aligntop"
                type="text"
                placeholder="Subject"
                v-model="template.subject"
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
                v-model="template.messages"
                size="mini"
              />
              <el-popconfirm
                confirmButtonText="Save and proceed"
                cancelButtonText="Cancel"
                icon="el-icon-info"
                iconColor="blue"
                title="Are you sure to save this template?"
                @onConfirm="create_template"
              >
                <el-button
                  slot="reference"
                  class="aligntop"
                  size="small"
                  type="primary"
                >Save Template</el-button>
              </el-popconfirm>

              <el-button
                @click="getsmstext"
                class="aligntop"
                size="small"
                type="primary"
                :loading="sendemailloading"
              >Test Result</el-button>
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
  name: "emailtemplates",
  data() {
    return {
      file: null,
      csvdetails: [],
      csvheader: [],
      newstringemail: [],
      search: "",
      sendemailloading: false,
      campaigns: [],
      template: {
        subject: "",
        messages: "",
        campaign: "",
        templatename: "",
        contacts_used: ""
      }
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
        })
        .catch(error => {
          console.log(error);
        });
    },
    create_template() {
      axios
        .post("/createtemplate", {
          data: this.csvdetails,
          template: this.template
        })
        .then(res => {
          if ((res.status = 201)) {
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
    },
    create_contacts() {
      axios
        .post("/createcontacts", { data: this.csvdetails })
        .then(res => {
          console.log(res.data);
        })
        .catch(error => {
          console.log(error);
        });
    },

    onFileChange(e) {
      var files = e.target.files || e.dataTransfer.files;
      if (!files.length) return;
      this.file = files[0];
      this.getexcelfiledetails();
    },
    newtext() {
      this.newstringemail = [];
      let s = this.template.subject;
      let m = this.template.messages;
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