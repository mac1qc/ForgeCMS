import React, { useState, useEffect } from 'react';
import axios from 'axios';

function App() {
  const [posts, setPosts] = useState([]);
  const [title, setTitle] = useState('');
  const [content, setContent] = useState('');

  const [editingPost, setEditingPost] = useState(null);

  useEffect(() => {
    axios.get('/api/posts')
      .then(response => {
        setPosts(response.data);
      })
      .catch(error => {
        console.log(error);
      });
  }, []);

  const handleCreatePost = () => {
    axios.post('/api/posts', { title, content })
      .then(response => {
        setPosts([...posts, response.data]);
        setTitle('');
        setContent('');
      })
      .catch(error => {
        console.log(error);
      });
  };

  const handleUpdatePost = (post) => {
    axios.put(`/api/posts/${post.id}`, post)
      .then(response => {
        setPosts(posts.map(p => (p.id === post.id ? response.data : p)));
        setEditingPost(null);
      })
      .catch(error => {
        console.log(error);
      });
  };

  const handleDeletePost = (id) => {
    axios.delete(`/api/posts/${id}`)
      .then(() => {
        setPosts(posts.filter(post => post.id !== id));
      })
      .catch(error => {
        console.log(error);
      });
  };

  return (
    <div>
      <h1>Posts</h1>
      <ul>
        {posts.map(post => (
          <li key={post.id}>
            <h2>{post.title}</h2>
            <p>{post.content}</p>
            <button onClick={() => handleDeletePost(post.id)}>Delete</button>
            <button onClick={() => setEditingPost(post)}>Edit</button>
          </li>
        ))}
      </ul>

      <h2>Create Post</h2>
      <input
        type="text"
        placeholder="Title"
        value={title}
        onChange={e => setTitle(e.target.value)}
      />
      <textarea
        placeholder="Content"
        value={content}
        onChange={e => setContent(e.target.value)}
      ></textarea>
      <button onClick={handleCreatePost}>Create</button>

      {editingPost && (
        <div>
          <h2>Edit Post</h2>
          <input
            type="text"
            placeholder="Title"
            value={editingPost.title}
            onChange={e => setEditingPost({ ...editingPost, title: e.target.value })}
          />
          <textarea
            placeholder="Content"
            value={editingPost.content}
            onChange={e => setEditingPost({ ...editingPost, content: e.target.value })}
          ></textarea>
          <button onClick={() => handleUpdatePost(editingPost)}>Update</button>
          <button onClick={() => setEditingPost(null)}>Cancel</button>
        </div>
      )}
    </div>
  );
}

export default App;
