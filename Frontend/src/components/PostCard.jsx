// src/components/PostCard.jsx
import React, { useEffect, useState } from 'react';
import { ArrowUp, ArrowDown } from 'lucide-react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import timeAgo from '../utils/timeAgo';

export default function PostCard({ post, currentUserId }) {
  const [score, setScore]         = useState(post.score);
  const [userVote, setUserVote]   = useState(null);

  // carregar score e userVote (mesma lógica anterior)
  useEffect(() => {
    const loadRating = async () => {
      try {
        const scoreRes = await axios.get(
          `http://localhost:8080/ratings-in-posts/${post.id}/score`
        );
        setScore(scoreRes.data.score);

        const votesRes = await axios.get(
          `http://localhost:8080/ratings-in-posts/${post.id}/votes`
        );
        const voteObj = votesRes.data.find(
          (v) => String(v.id_user) === String(currentUserId)
        );
        setUserVote(voteObj ? Boolean(voteObj.is_upvote) : null);
      } catch (err) {
        console.error('Erro carregando ratings:', err);
      }
    };

    loadRating();
  }, [post.id, currentUserId]);

  const handleVote = async (isUp) => {
    try {
      await axios.post(
        `http://localhost:8080/ratings-in-posts/${post.id}/votes`,
        { id_user: currentUserId, is_upvote: isUp }
      );
      // recarrega após votar
      const scoreRes = await axios.get(
        `http://localhost:8080/ratings-in-posts/${post.id}/score`
      );
      setScore(scoreRes.data.score);

      const votesRes = await axios.get(
        `http://localhost:8080/ratings-in-posts/${post.id}/votes`
      );
      const voteObj = votesRes.data.find(
        (v) => String(v.id_user) === String(currentUserId)
      );
      setUserVote(voteObj ? Boolean(voteObj.is_upvote) : null);
    } catch (err) {
      console.error('Erro no voto:', err);
    }
  };

  return (
    <div className="flex border rounded p-4 mb-4 bg-white shadow-sm">
      {/* Votação */}
      <div className="flex flex-col items-center w-12">
        <ArrowUp
          onClick={() => handleVote(true)}
          className={`w-5 h-5 cursor-pointer ${
            userVote === true
              ? 'text-green-600 hover:text-green-500'
              : 'text-gray-400 hover:text-green-500'
          }`}
        />
        <span className="font-bold mt-1">{score}</span>
        <ArrowDown
          onClick={() => handleVote(false)}
          className={`w-5 h-5 cursor-pointer ${
            userVote === false
              ? 'text-red-600 hover:text-red-500'
              : 'text-gray-400 hover:text-red-500'
          }`}
        />
      </div>

      {/* Conteúdo */}
      <div className="flex-1 pl-4">
        {/* Título leva para a página do post */}
        <Link
          to={`/posts/${post.id}`}
          className="text-xl font-medium text-gray-900 hover:underline"
        >
          {post.title}
        </Link>

        <div className="text-sm text-gray-500 mt-1">
          Submitted {timeAgo(post.submittedAt)} ago by{' '}
          {/* Nome leva para a página do usuário */}
          
          <Link to={`/users/${post.id_user}`} className="font-semibold hover:underline">
            {post.author}
          </Link>{' '}
          to{' '}
          {/* Comunidade leva para a página da comunidade */}
          <Link
            to={`/communities/${post.id_community}`}
            className="font-semibold hover:underline"
          >
            r/{post.community}
          </Link>
        </div>

        <div className="text-sm text-gray-600 mt-2">
          {/* Comentários leva também para o post */}
          <Link
            to={`/posts/${post.id}`}
            className="hover:underline"
          >
            {post.commentsCount} comment{post.commentsCount !== 1 && 's'}
          </Link>
        </div>
      </div>
    </div>
  );
}
