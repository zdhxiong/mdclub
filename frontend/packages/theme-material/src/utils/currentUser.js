export default function currentUser() {
  return window.app.user.getState().user;
}
