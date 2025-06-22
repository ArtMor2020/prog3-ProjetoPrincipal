import React, { useEffect, useState, useCallback } from "react";
import axios from "axios";
import { useParams } from "react-router-dom";
import { useUser } from "../contexts/UserContext";
import PostVoteHeader from "../components/PostVoteHeader";
import CommentCard from "../components/CommentCard";
import Header from "../components/Header";
import MentionTextarea from "../components/MentionTextarea";
import { Download } from "lucide-react";

const API_URL = "http://localhost:8080";

const AttachmentDisplay = ({ attachment }) => {
  const attachmentUrl = `${API_URL}/attachments/serve/${attachment.id}`;
  if (attachment.type === "IMAGE") {
    return (
      <img
        src={attachmentUrl}
        alt="Anexo do post"
        className="max-w-full rounded-lg my-4"
      />
    );
  }
  if (attachment.type === "VIDEO") {
    return (
      <video
        src={attachmentUrl}
        controls
        className="max-w-full rounded-lg my-4"
      />
    );
  }
  return (
    <a
      href={attachmentUrl}
      target="_blank"
      rel="noopener noreferrer"
      className="flex items-center p-3 my-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
    >
      <Download size={20} className="mr-3 text-gray-600" />
      <span>Baixar anexo ({attachment.type.toLowerCase()})</span>
    </a>
  );
};

export default function PostPage() {
  const { postId } = useParams();
  const { user } = useUser();

  const [post, setPost] = useState(null);
  const [attachments, setAttachments] = useState([]);
  const [commentsTree, setCommentsTree] = useState([]);
  const [newComment, setNewComment] = useState("");
  const [loading, setLoading] = useState(true);

  const loadComments = useCallback(async () => {
    if (!user) return;
    try {
      const { data } = await axios.get(
        `${API_URL}/comments/post/${postId}?viewerId=${user.id}`
      );
      const alive = data.filter((c) => c.is_deleted === "0");
      if (alive.length === 0) {
        setCommentsTree([]);
        return;
      }

      const byId = {};
      alive.forEach((c) => (byId[c.id] = { ...c, replies: [] }));

      const userPromises = alive.map((c) =>
        axios.get(`${API_URL}/users/${c.id_user}`)
      );
      const userResponses = await Promise.all(userPromises);

      userResponses.forEach((res, index) => {
        const commentId = alive[index].id;
        if (byId[commentId]) {
          byId[commentId].userName = res.data.name;
        }
      });

      const tree = [];
      Object.values(byId).forEach((c) => {
        if (c.id_parent_comment && byId[c.id_parent_comment]) {
          byId[c.id_parent_comment].replies.push(c);
        } else {
          tree.push(c);
        }
      });
      setCommentsTree(tree);
    } catch (err) {
      console.error("Erro ao carregar comentários:", err);
    }
  }, [postId]);

  useEffect(() => {
    const loadPostData = async () => {
      setLoading(true);
      try {
        const postRes = await axios.get(`${API_URL}/posts/${postId}`);
        const postData = postRes.data[0];
        const attachmentsData = postRes.data[1];

        if (!postData) {
          console.error("Post não encontrado");
          setPost(null);
          setLoading(false);
          return;
        }

        const userRes = await axios.get(`${API_URL}/users/${postData.id_user}`);
        let communityName = userRes.data.name;

        if (postData.id_community) {
          const commRes = await axios.get(
            `${API_URL}/communities/${postData.id_community}`
          );
          communityName = commRes.data.name;
        }

        setPost({
          ...postData,
          userName: userRes.data.name,
          communityName: communityName,
          isProfilePost: !postData.id_community,
        });
        setAttachments(attachmentsData);
      } catch (err) {
        console.error("Erro ao carregar post:", err);
      } finally {
        setLoading(false);
      }
    };

    loadPostData();
    loadComments();
  }, [postId, loadComments]);

  const submitComment = async (content, parentCommentId = null) => {
    if (!content.trim() || !user) return false;
    try {
      const payload = {
        id_user: user.id,
        id_parent_post: postId,
        id_parent_comment: parentCommentId,
        content: content.trim(),
      };
      await axios.post(`${API_URL}/comments/submit`, payload, {
        headers: { "Content-Type": "application/json" },
      });
      loadComments();
      return true;
    } catch (err) {
      console.error("Erro ao enviar comentário:", err);
      return false;
    }
  };

  const handleNewComment = async () => {
    const success = await submitComment(newComment);
    if (success) {
      setNewComment("");
    }
  };

  const handleReply = async (parentCommentId, text) => {
    return await submitComment(text, parentCommentId);
  };

  if (loading) return <div className="text-center p-10">Carregando...</div>;
  if (!post)
    return <div className="text-center p-10">Post não encontrado.</div>;

  return (
    <div>
      <Header onSearch={() => {}} />
      <div className="max-w-2xl mx-auto mt-6 space-y-6 mb-8">
        <PostVoteHeader
          postId={post.id}
          currentUserId={user.id}
          title={post.title}
          postedAt={post.posted_at}
          userName={post.userName}
          communityName={post.communityName}
          authorId={post.id_user}
          communityId={post.id_community}
          isProfilePost={post.isProfilePost}
        />

        <div className="border rounded p-4 bg-white shadow-sm">
          {post.description && (
            <div className="prose max-w-none mb-4">{post.description}</div>
          )}
          {attachments.length > 0 && (
            <div className="border-t pt-4 mt-4">
              {attachments.map((att) => (
                <AttachmentDisplay key={att.id} attachment={att} />
              ))}
            </div>
          )}
        </div>

        <div>
          <h2 className="text-xl font-semibold mb-4">Comentários</h2>
          <div className="mb-6">
            <MentionTextarea
              value={newComment}
              onChange={setNewComment}
              placeholder="Escreva seu comentário... Use u/username para mencionar."
              rows={3}
            />
            <button
              className="mt-2 px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 font-semibold"
              onClick={handleNewComment}
            >
              Enviar
            </button>
          </div>
          {commentsTree.map((c) => (
            <CommentCard
              key={c.id}
              comment={c}
              currentUserId={user.id}
              onReply={handleReply}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
