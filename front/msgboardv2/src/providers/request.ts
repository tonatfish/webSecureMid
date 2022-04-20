import axios from "axios";

const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
  timeout: 50000,
  // withCredentials: true // send cookies when cross-domain requests
});

// Request interceptors
service.interceptors.request.use(
  (config) => {
    // can skip if cors not happened
    config.headers["Access-Control-Allow-Origin"] =
      process.env.VUE_APP_BASE_API;
    config.headers["Access-Control-Allow-Methods"] =
      "GET, PATCH, POST, DELETE, OPTIONS";
    config.headers["Access-Control-Max-Age"] = "86400";

    if (!config.headers["Content-Type"]) {
      config.data = JSON.stringify(config.data);
      config.headers["Content-Type"] = "application/json";
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptors
service.interceptors.response.use(
  (response) => {
    // Some example codes here:
    // code == 20000: success
    // code == 50001: invalid access token
    // code == 50002: already login in other place
    // code == 50003: access token expired
    // code == 50004: invalid user (user not exist)
    // code == 50005: username or password is incorrect
    // You can change this part for your own usage.
    const res = response.data;
    if (
      response.status !== 200 &&
      response.status !== 201 &&
      response.status !== 204
    ) {
      // unknown success status code
      return Promise.reject(new Error(res.message || "Error"));
    } else {
      return response;
    }
  },
  async (error) => {
    switch (error.response.status) {
      case 422:
        console.log("invalid input");
        break;
      case 404:
        console.log("not found");
        break;
      default:
        console.log("some error");
    }
    return Promise.reject(error);
  }
);

export default service;
