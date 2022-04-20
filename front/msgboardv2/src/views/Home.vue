<template>
  <div class="home">
    <h1>This is an Home page</h1>
    <div v-if="!alreadyLogin">
      <h3>Login</h3>
      <el-form :inline="true" :model="loginInput" class="title-form-inline">
        <el-form-item label="User name:">
          <el-input
            v-model="loginInput.username"
            placeholder="user name"
          ></el-input>
        </el-form-item>
        <el-form-item label="Password:">
          <el-input
            v-model="loginInput.password"
            placeholder="password"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onLogin">Login</el-button>
        </el-form-item>
      </el-form>
      <h3>Register</h3>
      <el-form :model="registerInput" class="title-form-inline">
        <el-form-item label="User name:">
          <el-input
            v-model="registerInput.username"
            placeholder="user name"
          ></el-input>
        </el-form-item>
        <el-form-item label="Password:">
          <el-input
            v-model="registerInput.password"
            placeholder="password"
            show-password
          ></el-input>
        </el-form-item>
        <el-form-item label="Sticker image:">
          <el-upload
            class="upload-sticker"
            action="#"
            :http-request="handleHttpRequest"
            :limit="1"
            :file-list="stickerList"
            list-type="picture"
          >
            <el-button size="small" type="primary"
              >Upload Sticker & Register</el-button
            >
            <div slot="tip" class="el-upload__tip">Only image file</div>
          </el-upload>
        </el-form-item>
        <el-form-item label="Sticker url:">
          <el-input
            v-model="registerInput.weburl"
            placeholder="sticker url"
          ></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onRegister">Register</el-button>
        </el-form-item>
        {{ registerResponse }}
      </el-form>
    </div>
    <div v-else>
      <span> Hello, {{ userInfo.name }} </span>
      <el-image
        style="width: 100px; height: 100px"
        :src="'data:image/jpeg;base64,' + userInfo.sticker"
        :fit="fill"
      ></el-image>
      <el-button type="primary" @click="onLogout">Logout</el-button>
    </div>
  </div>
</template>

<script lang="ts">
import { createUser, getUser, loginUser, logoutUser } from "@/api/users";
import { Vue, Component } from "vue-property-decorator";
import { getItem, setItem } from "@/providers/cookies.ts";
import request from "@/providers/request";

@Component({})
export default class extends Vue {
  private loginInput = {
    username: "",
    password: "",
  };
  private userInfo = {
    id: "",
    token: "",
    name: "",
    sticker: "",
  };
  private registerInput = {
    username: "",
    password: "",
    weburl: "",
  };
  private stickerList: any[] = [];
  private Changeresponse = "";
  private alreadyLogin = false;
  private registerResponse = "";

  async created() {
    const token = getItem("token") as string;
    this.alreadyLogin = token.length > 0;
    if (this.alreadyLogin) {
      const reqInfo = {
        userid: getItem("userid") as string,
        token: getItem("token") as string,
      };
      try {
        const { data: userInfo } = await getUser(reqInfo);
        this.userInfo = userInfo;
      } catch (err) {
        this.Changeresponse = err;
        console.log(err);
      }
    }
  }

  private async onLogin() {
    const reqInfo = {
      username: this.loginInput.username,
      password: this.loginInput.password,
    };
    try {
      const { data: userInfo } = await loginUser(reqInfo);
      setItem("userid", userInfo.id);
      setItem("token", userInfo.token);
      this.userInfo = userInfo;
      this.alreadyLogin = true;
    } catch (err) {
      console.log(err);
    }
  }

  private async onLogout() {
    const reqInfo = {
      userid: getItem("userid") as string,
      token: getItem("token") as string,
    };
    try {
      const { data: logout } = await logoutUser(reqInfo);
      setItem("userid", "");
      setItem("token", "");
      this.userInfo = {
        id: "",
        token: "",
        name: "",
        sticker: "",
      };
      this.alreadyLogin = false;
    } catch (err) {
      console.log(err);
    }
  }

  private async handleHttpRequest(param: any) {
    console.log(param);
    console.log(this.registerInput);
    const formData = new FormData();
    formData.append("username", this.registerInput.username);
    formData.append("password", this.registerInput.password);
    formData.append("sticker", param.file);
    try {
      const { data } = await request({
        url: "/user",
        data: formData,
        method: "post",
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      if (data) {
        this.registerResponse = "File Error!";
      } else {
        this.registerResponse = "Register with upload Successful!";
      }
    } catch (err) {
      this.registerResponse = "Error!";
      console.log(err);
    }
  }

  private async onRegister() {
    try {
      const { data: userInfo } = await createUser(this.registerInput);
      this.registerResponse = "Register with url Successful!";
    } catch (err) {
      this.registerResponse = "Error!";
      console.log(err);
    }
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
