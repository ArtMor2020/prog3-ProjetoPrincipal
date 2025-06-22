import React, { useEffect, useState } from "react";
import { ArrowUp, ArrowDown } from "lucide-react";
import { Link } from "react-router-dom";
import axios from "axios";
import timeAgo from "../utils/timeAgo";

export default function PostVoteHeader({
  postId,
  currentUserId,
  title,
  postedAt,
  userName,
  communityName,
  authorId,
  communityId,
  isProfilePost,
}) {
  const [score, setScore] = useState(0);
  const [userVote, setUserVote] = useState(null);

  const loadRating = async () => {
    try {
      const scoreRes = await axios.get(
        `http://localhost:8080/ratings-in-posts/${postId}/score`
      );
      setScore(scoreRes.data.score);

      const votesRes = await axios.get(
        `http://localhost:8080/ratings-in-posts/${postId}/votes`
      );
      const yourVote = votesRes.data.find(
        (v) => String(v.id_user) === String(currentUserId)
      );
      setUserVote(yourVote ? Boolean(Number(yourVote.is_upvote)) : null);
    } catch (error) {
      if (error.response?.status !== 404) {
        console.error(`Erro ao carregar rating para post ${postId}:`, error);
      }
    }
  };

  useEffect(() => {
    if (postId && currentUserId) {
      loadRating();
    }
  }, [postId, currentUserId]);

  const handleVote = async (isUp) => {
    await axios.post(`http://localhost:8080/ratings-in-posts/${postId}/votes`, {
      id_user: currentUserId,
      is_upvote: isUp,
    });
    await loadRating();
  };

  return (
    <div className="flex border rounded p-4 bg-white shadow-sm mb-4">
      <div className="flex flex-col items-center w-12 text-gray-600">
        <ArrowUp
          onClick={() => handleVote(true)}
          className={`w-5 h-5 cursor-pointer ${
            userVote === true
              ? "text-green-600 hover:text-green-500"
              : "text-gray-400 hover:text-green-500"
          }`}
        />
        <span className="font-bold mt-1">{score}</span>
        <ArrowDown
          onClick={() => handleVote(false)}
          className={`w-5 h-5 cursor-pointer ${
            userVote === false
              ? "text-red-600 hover:text-red-500"
              : "text-gray-400 hover:text-red-500"
          }`}
        />
      </div>

      <div className="flex-1 pl-4">
        <h1 className="text-2xl font-bold">{title}</h1>
        <div className="text-sm text-gray-500 mt-1">
          Submitted {timeAgo(postedAt)} ago by{" "}
          <Link
            to={`/users/${authorId}`}
            className="font-semibold hover:underline"
          >
            u/{userName}
          </Link>
          {!isProfilePost && communityId && (
            <>
              {" to "}
              <Link
                to={`/communities/${communityId}`}
                className="font-semibold hover:underline"
              >
                r/{communityName}
              </Link>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
