import React, { useState, useEffect } from "react";
import { useLocation } from "react-router-dom";
import axios from "axios";
import Header from "../components/Header";
import PostCard from "../components/PostCard";
import { useUser } from "../contexts/UserContext";

function useQuery() {
  return new URLSearchParams(useLocation().search);
}

export default function SearchResultsPage() {
  const { user } = useUser();
  const query = useQuery().get("q");
  const [results, setResults] = useState({
    posts: [],
    communities: [],
    users: [],
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (!query) return;
    setLoading(true);
    axios
      .get(`http://localhost:8080/search/${query}`)
      .then(async (res) => {
        const enrichedPosts = await Promise.all(
          res.data.posts.map(async (post) => {
            const userRes = await axios.get(
              `http://localhost:8080/users/${post.id_user}`
            );
            let commName = userRes.data.name;
            if (post.id_community) {
              const commRes = await axios.get(
                `http://localhost:8080/communities/${post.id_community}`
              );
              commName = commRes.data.name;
            }
            return {
              ...post,
              author: userRes.data.name,
              community: commName,
              isProfilePost: !post.id_community,
            };
          })
        );
        setResults({ ...res.data, posts: enrichedPosts });
      })
      .catch((err) => console.error("Erro na página de busca:", err))
      .finally(() => setLoading(false));
  }, [query]);

  return (
    <div>
      <Header />
      <div className="max-w-2xl mx-auto mt-6 space-y-6">
        <h1 className="text-2xl font-bold">
          Resultados da busca por: "{query}"
        </h1>

        {loading ? (
          <p>Carregando...</p>
        ) : (
          <>
            {results.posts.length > 0 ? (
              results.posts.map((post) => (
                <PostCard key={post.id} post={post} currentUserId={user.id} />
              ))
            ) : (
              <p className="text-gray-500">Nenhum post encontrado.</p>
            )}
          </>
        )}
      </div>
    </div>
  );
}
