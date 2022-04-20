import request from "@/providers/request";

export const getMessages = (data: any) =>
  request({
    url: "/msg/get",
    method: "post",
    data,
  });

export const getMessage = (id: string, data: any) =>
  request({
    url: `/msg/get/${id}`,
    method: "post",
    data,
  });

export const createMessage = (data: any) =>
  request({
    url: "/msg",
    method: "post",
    data,
  });

export const deleteMessage = (id: string, data: any) =>
  request({
    url: `/msg/${id}`,
    method: "delete",
    data,
  });
