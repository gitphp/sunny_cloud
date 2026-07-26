<?php

namespace App\Http\Resources\backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'art_cover' => $this->art_cover,
            'art_content' => $this->art_content,
            'content_type' => $this->content_type?->value,
            'content_type_label' => $this->content_type?->label(),
            'summary' => $this->summary,
            'category_id' => (string) $this->category_id,
            'category_name' => $this->category?->cat_name ?? '',
            'tag_ids' => $this->tag_ids ?? [],
            'author_id' => (string) $this->author_id,
            'author_name' => $this->author_name,
            'source' => $this->source,
            'source_url' => $this->source_url,
            'art_status' => $this->art_status?->value,
            'art_status_label' => $this->art_status?->label(),
            'is_top' => $this->is_top?->value,
            'is_original' => $this->is_original?->value,
            'is_commentable' => $this->is_commentable?->value,
            'seo_title' => $this->seo_title,
            'seo_keywords' => $this->seo_keywords,
            'seo_description' => $this->seo_description,
            'extra_fields' => $this->extra_fields,
            'view_count' => (int) $this->view_count,
            'like_count' => (int) $this->like_count,
            'collect_count' => (int) $this->collect_count,
            'share_count' => (int) $this->share_count,
            'comment_count' => (int) $this->comment_count,
            'published_at' => optional($this->published_at)?->format('Y-m-d H:i:s'),
            'reviewer_id' => (string) $this->reviewer_id,
            'reviewed_at' => optional($this->reviewed_at)?->format('Y-m-d H:i:s'),
            'reject_reason' => $this->reject_reason,
            'created_at' => optional($this->created_at)?->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
