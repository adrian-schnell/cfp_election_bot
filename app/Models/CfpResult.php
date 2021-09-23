<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 * @property string  title
 * @property integer github_issue_id
 * @property integer yes
 * @property integer no
 * @property integer neutral
 * @property integer votes_total
 * @property integer possible_votes
 * @property float   vote_turnout
 * @property string  current_result
 * @property string  github_uri
 */
class CfpResult extends Model
{
    protected $fillable = [
        'title',
        'github_issue_id',
        'yes',
        'no',
        'neutral',
        'votes_total',
        'possible_votes',
        'vote_turnout',
        'current_result',
    ];
    protected $appends = ['github_uri'];
    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function getGitHubUriAttribute(): string
    {
        return sprintf('https://github.com/DeFiCh/dfips/issues/%s', $this->github_issue_id);
    }
}
