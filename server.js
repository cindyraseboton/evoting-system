const express = require('express');
const mongoose = require('mongoose');
const dotenv = require('dotenv');
const path = require('path');

dotenv.config();
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));
app.set('view engine', 'ejs');

mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/evoting')
  .then(() => console.log('✅ Connected to MongoDB'))
  .catch(err => console.error('❌ DB Error:', err));

app.get('/', (req, res) => {
  res.send(`
    <html>
      <body style="font-family:Arial; max-width:600px; margin:50px auto; padding:20px; text-align:center;">
        <h1>🗳️ Student E-Voting System</h1>
        <p style="color:green; font-size:18px;">✅ System is running!</p>
        <p>Final Project — IT13A</p>
      </body>
    </html>
  `);
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`🚀 Server live on port ${PORT}`));
