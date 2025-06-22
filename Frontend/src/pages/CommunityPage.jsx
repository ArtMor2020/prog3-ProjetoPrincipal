import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import PostCard from "../components/PostCard";
import { useUser } from "../contexts/UserContext";
import CommunityJoinButton from "../components/CommunityJoinButton";

export default function CommunityPage() {
  const { communityId } = useParams();
  const { user } = useUser();
  const [community, setCommunity] = useState(null);
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchCommunityAndPosts = async () => {
      try {
        const commRes = await axios.get(
          `http://localhost:8080/communities/${communityId}`
        );
        setCommunity(commRes.data);

        const postsRes = await axios.get("http://localhost:8080/posts");
        const commPosts = postsRes.data.filter(
          (p) =>
            String(p.id_community) === String(communityId) &&
            p.is_deleted === "0"
        );
        const enriched = await Promise.all(
          commPosts.map(async (post) => {
            const [userRes, commentsRes] = await Promise.all([
              axios.get(`http://localhost:8080/users/${post.id_user}`),
              axios.get(`http://localhost:8080/comments/post/${post.id}`),
            ]);
            return {
              ...post,
              author: userRes.data.name,
              community: commRes.data.name,
              commentsCount: Array.isArray(commentsRes.data)
                ? commentsRes.data.length
                : 0,
            };
          })
        );
        setPosts(enriched);
      } catch (err) {
        console.error("Erro ao carregar comunidade e posts:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchCommunityAndPosts();
  }, [communityId]);

  if (loading) return <div className="text-center mt-10">Carregando...</div>;
  if (!community)
    return <div className="text-center mt-10">Comunidade não encontrada.</div>;

  return (
    <div>
      <Header onSearch={() => {}} />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        <div className="bg-white shadow-sm rounded p-6">
          <div className="flex items-center justify-between">
            <h1 className="text-2xl font-bold">r/{community.name}</h1>
            <CommunityJoinButton communityId={parseInt(communityId, 10)} />
          </div>
          {community.description && (
            <p className="mt-2 text-gray-700">{community.description}</p>
          )}
        </div>
        <div className="space-y-4">
          <h2 className="text-xl font-semibold">Posts em r/{community.name}</h2>
          {posts.length > 0 ? (
            posts.map((post) => (
              <PostCard key={post.id} post={post} currentUserId={user.id} />
            ))
          ) : (
            <p className="text-gray-600">Nenhum post nesta comunidade ainda.</p>
          )}
        </div>
      </div>
    </div>
  );
}
