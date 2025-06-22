import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import PostCard from "../components/PostCard";
import FriendshipButton from "../components/FriendshipButton";
import BlockUserButton from "../components/BlockUserButton";
import { useUser } from "../contexts/UserContext";

export default function UserPage() {
  const { userId } = useParams();
  const { user: loggedInUser } = useUser();
  const [profileUser, setProfileUser] = useState(null);
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchUserAndPosts = async () => {
      if (!loggedInUser) return;
      setLoading(true);
      try {
        const userRes = await axios.get(
          `http://localhost:8080/users/${userId}`
        );
        setProfileUser(userRes.data);

        const postsRes = await axios.get(
          `http://localhost:8080/posts?viewerId=${loggedInUser.id}`
        );
        const userPosts = postsRes.data.filter(
          (p) => String(p.id_user) === String(userId)
        );

        const enriched = await Promise.all(
          userPosts.map(async (post) => {
            let communityName = userRes.data.name;
            if (post.id_community) {
              const commRes = await axios.get(
                `http://localhost:8080/communities/${post.id_community}`
              );
              communityName = commRes.data.name;
            }
            const commentsRes = await axios.get(
              `http://localhost:8080/comments/post/${post.id}?viewerId=${loggedInUser.id}`
            );
            return {
              ...post,
              author: userRes.data.name,
              community: communityName,
              isProfilePost: !post.id_community,
              commentsCount: Array.isArray(commentsRes.data)
                ? commentsRes.data.length
                : 0,
            };
          })
        );
        setPosts(enriched);
      } catch (err) {
        console.error("Erro ao carregar perfil e posts:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchUserAndPosts();
  }, [userId, loggedInUser]);

  if (loading)
    return (
      <div>
        <Header />
        <div className="text-center mt-10">Carregando...</div>
      </div>
    );
  if (!profileUser)
    return (
      <div>
        <Header />
        <div className="text-center mt-10">Usuário não encontrado.</div>
      </div>
    );

  return (
    <div>
      <Header onSearch={() => {}} />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        <div className="bg-white shadow-sm rounded p-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold">{profileUser.name}</h1>
            {loggedInUser &&
              String(loggedInUser.id) !== String(profileUser.id) && (
                <div className="flex items-center space-x-2">
                  <FriendshipButton profileUserId={profileUser.id} />
                  <BlockUserButton profileUserId={profileUser.id} />
                </div>
              )}
          </div>
          {profileUser.about && (
            <p className="mt-2 text-gray-700">{profileUser.about}</p>
          )}
        </div>
        <div className="space-y-4">
          <h2 className="text-xl font-semibold">Posts de {profileUser.name}</h2>
          {posts.length > 0 ? (
            posts.map((post) => (
              <PostCard
                key={post.id}
                post={post}
                currentUserId={loggedInUser.id}
              />
            ))
          ) : (
            <p className="text-gray-600">
              Este usuário ainda não criou nenhum post.
            </p>
          )}
        </div>
      </div>
    </div>
  );
}
