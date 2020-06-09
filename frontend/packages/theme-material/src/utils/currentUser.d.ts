import { User } from "mdclub-sdk-js/es/models";

/**
 * 获取当前登录用户信息，未登录则为 null
 */
export default declare function currentUser(): User;
