import React from "react";
import { Switch, Route, Redirect } from "react-router-dom";
import "./App.css";
import LoginPage from "./pages/Login";
import RegisterPage from "./pages/Register";
import HomePage from "./pages/HomePage";
import PostPage from "./pages/PostPage";
import UserPage from "./pages/UserPage";
import CommunityPage from "./pages/CommunityPage";
import { useUser } from "./contexts/UserContext";
import PostCreate from "./pages/PostCreate";
import CommunityCreatePage from "./pages/CommunityCreatePage";
import ChatBar from "./components/ChatBar";
import SearchResultsPage from "./pages/SearchResultsPage";

function MainLayout({ children }) {
  const { user } = useUser();
  return (
    <>
      {children}
      {user && <ChatBar />}
    </>
  );
}

function PrivateRoute({ children, ...rest }) {
  const { user } = useUser();
  return (
    <Route
      {...rest}
      render={() =>
        user ? <MainLayout>{children}</MainLayout> : <Redirect to="/login" />
      }
    />
  );
}

export default function App() {
  return (
    <Switch>
      <Route exact path="/">
        <Redirect to="/login" />
      </Route>

      <Route exact path="/login" component={LoginPage} />
      <Route exact path="/register" component={RegisterPage} />

      <PrivateRoute exact path="/home">
        <HomePage />
      </PrivateRoute>

      <PrivateRoute exact path="/posts/:postId">
        <PostPage />
      </PrivateRoute>

      <PrivateRoute path="/users/:userId">
        <UserPage />
      </PrivateRoute>

      <PrivateRoute path="/communities/:communityId">
        <CommunityPage />
      </PrivateRoute>

      <PrivateRoute exact path="/post/create">
        <PostCreate />
      </PrivateRoute>

      <PrivateRoute exact path="/community/create">
        <CommunityCreatePage />
      </PrivateRoute>

      <PrivateRoute path="/search-results">
        <SearchResultsPage />
      </PrivateRoute>

      <Route>
        <Redirect to="/home" />
      </Route>
    </Switch>
  );
}
