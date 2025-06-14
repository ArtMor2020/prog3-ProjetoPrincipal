import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useParams, Link } from 'react-router-dom';
import { useUser } from '../contexts/UserContext';
import PostVoteHeader from '../components/PostVoteHeader';
import CommentCard from '../components/CommentCard';
import Header from '../components/Header';

export default function PostPage() {
  const { postId } = useParams();
  const { user }   = useUser();

  const [post, setPost] = useState(null);
  const [commentsTree, setCommentsTree] = useState([]);
  const [newComment, setNewComment] = useState('');

  // Carrega post e enriquece com nome de autor e comunidade
  useEffect(() => {
    const loadPost = async () => {
      try {
        const { data } = await axios.get(`http://localhost:8080/posts/${postId}`);
        // busca autor e comunidade em paralelo
        const [userRes, commRes] = await Promise.all([
          axios.get(`http://localhost:8080/users/${data.id_user}`),
          axios.get(`http://localhost:8080/communities/${data.id_community}`)
        ]);
        setPost({
          ...data,
          userName: userRes.data.name,
          communityName: commRes.data.name
        });
      } catch (err) {
        console.error('Erro ao carregar post:', err);
      }
    };

    loadPost();
    loadComments();
  }, [postId]);

  // Função para carregar comentários e montar árvore
  const loadComments = async () => {
    try {
      const { data } = await axios.get(`http://localhost:8080/comments/post/${postId}`);
      const alive = data.filter(c => c.is_deleted === "0");
      const byId = {};
      alive.forEach(c => byId[c.id] = { ...c, replies: [], userName: '' });

      // busca nomes de usuários
      await Promise.all(alive.map(async c => {
        const res = await axios.get(`http://localhost:8080/users/${c.id_user}`);
        byId[c.id].userName = res.data.name;
      }));

      // monta árvore de replies
      const tree = [];
      Object.values(byId).forEach(c => {
        if (c.id_parent_comment) {
          byId[c.id_parent_comment]?.replies.push(c);
        } else {
          tree.push(c);
        }
      });

      setCommentsTree(tree);
    } catch (err) {
      console.error('Erro ao carregar comentários:', err);
    }
  };

  // criar comentário no nível do post
  const handleNewComment = async () => {
    if (!newComment.trim()) return;
    try {
      await axios.post('http://localhost:8080/comments', {
        id_user: user.id,
        id_parent_post: postId,
        id_parent_comment: null,
        content: newComment,
      });
      setNewComment('');
      loadComments();
    } catch (err) {
      console.error('Erro ao comentar no post:', err);
    }
  };

  // responder a um comentário específico
  const handleReply = async (parentCommentId, text) => {
    if (!text.trim()) return;
    try {
      await axios.post('http://localhost:8080/comments', {
        id_user: user.id,
        id_parent_post: postId,
        id_parent_comment: parentCommentId,
        content: text,
      });
      loadComments();
    } catch (err) {
      console.error('Erro ao responder comentário:', err);
    }
  };

  if (!post) return <div>Loading...</div>;

  return (
    <div>
      <Header onSearch={() => {}} />
    <div className="max-w-2xl mx-auto mt-6 space-y-6">
      {/* Header do post com votação */}
      <PostVoteHeader
        postId={post.id}
        currentUserId={user.id}
        title={post.title}
        postedAt={post.posted_at}
        userName={post.userName}
        communityName={post.communityName}
        authorId={post.id_user}
        communityId={post.id_community}
      />

      {/* Descrição */}
      <div className="border rounded p-4 bg-white shadow-sm">
        {post.description}
      </div>

      {/* Lista de comentários */}
      <div>
        <h2 className="text-xl font-semibold mb-4">Comments</h2>
              {/* Novo comentário */}
      <div className="mb-6">
        <textarea
          className="w-full border rounded p-2"
          rows="3"
          placeholder="Escreva seu comentário..."
          value={newComment}
          onChange={e => setNewComment(e.target.value)}
        />
        <button
          className="mt-2 px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-500"
          onClick={handleNewComment}
        >
          Enviar
        </button>
      </div>
        {commentsTree.map(c => (
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
