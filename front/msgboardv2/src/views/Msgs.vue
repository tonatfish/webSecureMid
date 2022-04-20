<template>
  <div class="msgs">
    <h1>This is an message list page</h1>
    <div v-if="loggedIn">
      welcome! {{ username }}
      <el-form :model="msgInput" class="title-form-inline">
        <el-form-item label="Content(BBcode):">
          <el-input
            v-model="msgInput.content"
            placeholder="content"
            type="textarea"
            :rows="10"
          ></el-input>
        </el-form-item>
        <el-form-item label="Attach file(optional):">
          <el-upload
            class="upload-file"
            action="#"
            :http-request="handleHttpRequest"
            :limit="1"
            :file-list="fileList"
          >
            <el-button size="small" type="primary"
              >Upload File & Leave Message</el-button
            >
          </el-upload>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onCreateMessage"
            >Leave Message</el-button
          >
        </el-form-item>
        {{ msgCreateResponse }}
      </el-form>
      <el-table
        :data="msgList"
        border
        fit
        highlight-current-row
        style="width: 100%"
      >
        <el-table-column
          label="From"
          align="center"
          prop="username"
          width="160px"
        >
          <template slot-scope="{ row }">
            <span>{{ row.username }}</span>
          </template>
        </el-table-column>
        <el-table-column label="Content" align="center" sortable="custom">
          <template slot-scope="{ row }">
            <span v-html="row.content"></span>
          </template>
        </el-table-column>
        <el-table-column label="file" align="center" width="150px">
          <template slot-scope="{ row }">
            <span>{{ row.filename }}</span>
          </template>
        </el-table-column>
        <el-table-column
          label="Actions"
          align="center"
          width="300px"
          class-name="fixed-width"
        >
          <template slot-scope="{ row }">
            <el-button size="mini" type="info" @click="handleView(row.id)">
              Look
            </el-button>
            <el-button
              v-if="row.showDelete"
              size="mini"
              type="primary"
              @click="handleDelete(row.id)"
            >
              Delete
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div v-else>nope, you're not logged in.</div>
  </div>
</template>

<script lang="ts">
import { getMessages, createMessage, deleteMessage } from "@/api/msgs";
import { getUser } from "@/api/users";
import { Vue, Component } from "vue-property-decorator";
import { getItem } from "@/providers/cookies.ts";
import request from "@/providers/request";
import BBCodeParser from "ts-bbcode-parser";
import VueRouter, { Route } from "vue-router";

declare module "vue/types/vue" {
  interface Vue {
    $router: VueRouter;
  }
}

@Component({})
export default class extends Vue {
  private msgInput = {
    content: "",
  };
  private fileList: any[] = [];
  private msgList: any[] = [];
  private msgCreateResponse = "";
  private loggedIn = false;
  private username = "";
  private bbCodeParser = new BBCodeParser();

  async created() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
    };
    if (reqInfo.userid.length > 0 && reqInfo.token.length > 0) {
      try {
        const { data: userInfo } = await getUser(reqInfo);
        this.username = userInfo.name;
        this.loggedIn = true;
      } catch (err) {
        this.loggedIn = false;
        console.log(err);
      }
    }
    if (this.loggedIn) {
      await this.loadMessages();
    }
  }

  private async onCreateMessage() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
      content: this.msgInput.content,
    };
    try {
      const { data: title } = await createMessage(reqInfo);
      this.msgCreateResponse = "Create Message Successful!";
    } catch (err) {
      this.msgCreateResponse = "Error!";
      console.log(err);
    }
    await this.loadMessages();
  }

  private async loadMessages() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
      content: this.msgInput.content,
    };
    try {
      const { data: messagesInfo } = await getMessages(reqInfo);
      this.msgList = messagesInfo;
      for (let i = 0; i < this.msgList.length; i++) {
        this.msgList[i].showDelete = this.username === this.msgList[i].username;
        if (
          this.msgList[i].content !== null &&
          this.msgList[i].content.length > 0
        ) {
          this.msgList[i].content = this.bbCodeParser.parse(
            this.msgList[i].content
          );
        }
      }
      console.log(this.msgList);
    } catch (err) {
      console.log(err);
    }
  }

  private async handleHttpRequest(param: any) {
    const formData = new FormData();
    formData.append("userid", getItem("userid") as string);
    formData.append("token", getItem("token") as string);
    formData.append("content", this.msgInput.content);
    formData.append("file", param.file);
    try {
      const { data } = await request({
        url: "/msg",
        data: formData,
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (data) {
        this.msgCreateResponse = "File Error!";
      } else {
        this.msgCreateResponse = "Create Message with upload Successful!";
      }
    } catch (err) {
      this.msgCreateResponse = "Error!";
      console.log(err);
    }
    this.loadMessages();
  }

  private handleView(id: string) {
    console.log(id);
    this.$router.push("/msg/" + id);
  }

  private async handleDelete(id: string) {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
    };
    try {
      const { data: del } = await deleteMessage(id, reqInfo);
      this.msgCreateResponse = "Delete Message Successful!";
    } catch (err) {
      this.msgCreateResponse = "Error!";
      console.log(err);
    }
    this.loadMessages();
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
