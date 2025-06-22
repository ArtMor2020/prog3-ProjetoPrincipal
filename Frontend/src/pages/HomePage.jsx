import React, { useEffect, useState } from "react";
import axios from "axios";
import PostCard from "../components/PostCard";
import Header from "../components/Header";
import { useUser } from "../contexts/UserContext";

export default function HomePage() {
  const { user } = useUser();
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const postsPerPage = 10;

  useEffect(() => {
    const fetchPosts = async () => {
      if (!user) {
        setLoading(false);
        return;
      }
      setLoading(true);
      try {
        const { data } = await axios.get(
          `http://localhost:8080/posts?viewerId=${user.id}`
        );
        const enriched = await Promise.all(
          data
            .filter((post) => post.is_deleted === "0")
            .map(async (post) => {
              try {
                const userRes = await axios.get(
                  `http://localhost:8080/users/${post.id_user}`
                );
                let communityName = userRes.data.name;
                if (post.id_community) {
                  const commRes = await axios.get(
                    `http://localhost:8080/communities/${post.id_community}`
                  );
                  communityName = commRes.data.name;
                }
                const commentsRes = await axios.get(
                  `http://localhost:8080/comments/post/${post.id}?viewerId=${user.id}`
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
              } catch (error) {
                console.error(`Falha ao enriquecer post ID ${post.id}:`, error);
                return null;
              }
            })
        );
        setPosts(enriched.filter((p) => p !== null));
      } catch (err) {
        console.error("Erro ao carregar posts:", err);
      } finally {
        setLoading(false);
      }
    };
    fetchPosts();
  }, [user]);

  const totalPages = Math.ceil(posts.length / postsPerPage);

  if (loading) {
    return (
      <div>
        <Header onSearch={() => {}} />
        <div className="text-center mt-10">Carregando posts...</div>
      </div>
    );
  }

  const start = (page - 1) * postsPerPage;
  const currentPosts = posts.slice(start, start + postsPerPage);

  return (
    <div>
      <Header onSearch={() => {}} />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        {currentPosts.length > 0 ? (
          currentPosts.map((post) => (
            <PostCard key={post.id} post={post} currentUserId={user.id} />
          ))
        ) : (
          <div className="text-center text-gray-500 py-10">
            Nenhum post para mostrar.
          </div>
        )}
        {totalPages > 1 && (
          <div className="flex flex-col items-center py-4">
            <span className="mb-2 text-gray-600 font-medium">Páginas</span>
            <div className="flex items-center space-x-4">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page === 1}
                className="p-2 rounded-full bg-gray-200 disabled:opacity-50 hover:bg-gray-300"
              >
                ←
              </button>
              <div className="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white font-bold">
                {page}
              </div>
              <button
                onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                disabled={page === totalPages || totalPages === 0}
                className="p-2 rounded-full bg-gray-200 disabled:opacity-50 hover:bg-gray-300"
              >
                →
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
