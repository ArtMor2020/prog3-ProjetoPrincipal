import React, { useState, useEffect } from "react";
import { ArrowUp, ArrowDown } from "lucide-react";
import { Link } from "react-router-dom";
import axios from "axios";
import timeAgo from "../utils/timeAgo";
import MentionTextarea from "./MentionTextarea";

export default function CommentCard({
  comment,
  currentUserId,
  level = 0,
  onReply,
}) {
  const [score, setScore] = useState(0);
  const [userVote, setUserVote] = useState(null);
  const [replying, setReplying] = useState(false);
  const [replyText, setReplyText] = useState("");

  const loadRating = async () => {
    if (!comment || !comment.id) return;
    try {
      const { data: scoreData } = await axios.get(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes`
      );
      setScore(scoreData.score);

      const { data: votes } = await axios.get(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes/list`
      );
      const myVote = votes.find(
        (v) => String(v.id_user) === String(currentUserId)
      );
      setUserVote(myVote ? Boolean(Number(myVote.is_upvote)) : null);
    } catch (err) {
      if (err.response?.status !== 404) {
        console.error("Erro carregando ratings do comentário:", err);
      }
    }
  };

  useEffect(() => {
    loadRating();
  }, [comment.id, currentUserId]);

  const handleVote = async (isUp) => {
    try {
      await axios.post(
        `http://localhost:8080/ratings-in-comments/${comment.id}/votes`,
        { id_user: currentUserId, is_upvote: isUp }
      );
      await loadRating();
    } catch (err) {
      console.error("Erro ao votar:", err);
    }
  };

  const submitReply = async () => {
    const success = await onReply(comment.id, replyText);
    if (success) {
      setReplyText("");
      setReplying(false);
    }
  };

  if (!comment) return null;

  return (
    <div
      className={`mb-4 rounded p-3 bg-white shadow-sm ${
        level > 0 ? "border-l-2 border-gray-200 pl-4" : ""
      }`}
    >
      <div className="flex rounded p-3 bg-white">
        <div className="flex flex-col items-center w-12 text-gray-600">
          <ArrowUp
            className={`w-5 h-5 cursor-pointer ${
              userVote === true ? "text-green-600" : "hover:text-green-500"
            }`}
            onClick={() => handleVote(true)}
          />
          <span className="font-bold mt-1">{score}</span>
          <ArrowDown
            className={`w-5 h-5 cursor-pointer ${
              userVote === false ? "text-red-600" : "hover:text-red-500"
            }`}
            onClick={() => handleVote(false)}
          />
        </div>
        <div className="flex-1 pl-4">
          <div className="text-sm text-gray-500">
            <Link
              to={`/users/${comment.id_user}`}
              className="font-semibold hover:underline"
            >
              {comment.userName}
            </Link>{" "}
            • {timeAgo(comment.created_at)} ago
          </div>
          <div className="mt-1">{comment.content}</div>
          <div
            className="text-sm text-gray-500 font-semibold cursor-pointer hover:underline mt-2"
            onClick={() => setReplying(!replying)}
          >
            comentar
          </div>
          {replying && (
            <div className="mt-2">
              <MentionTextarea
                value={replyText}
                onChange={setReplyText}
                placeholder="Sua resposta... Use u/username para mencionar."
                rows={2}
              />
              <button
                className="mt-1 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500"
                onClick={submitReply}
              >
                Enviar
              </button>
            </div>
          )}
        </div>
      </div>
      {comment.replies &&
        comment.replies.map((reply) => (
          <CommentCard
            key={reply.id}
            comment={reply}
            currentUserId={currentUserId}
            level={level + 1}
            onReply={onReply}
          />
        ))}
    </div>
  );
}
